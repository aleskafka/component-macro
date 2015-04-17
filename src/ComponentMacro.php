<?php

use Latte\Engine;
use Latte\Macros\MacroSet;
use Latte\MacroNode;
use Latte\PhpWriter;


/**
 * - n:component
 * - n:child
 * - n:key
 */
class ComponentMacro extends MacroSet
{

	/** @var string */
	private static $dir = '';


	public static function install(Engine $latte, $dir)
	{
		$me = new self($latte->getCompiler());
		$me::$dir = $dir;

		$me->addMacro('component', NULL, [$me, 'macroComponentEnd']);
		$me->addMacro('child', NULL, [$me, 'macroChildEnd']);
		$me->addMacro('key', NULL, [$me, 'macroKeyEnd']);
	}


	/**
	 * Finishes template parsing.
	 * @return array(prolog, epilog)
	 */
	public function finalize()
	{
		return array('$_components=isset($_components) ? (array)$_components : array();', '');
	}


	public function componentTemplate($name)
	{
		return self::$dir . '/' . ltrim(preg_replace_callback('#[A-Z]#', function($m) {
			return '/' . mb_strtolower($m[0]);
		}, $name), '/') . '.latte';
	}


	public function macroComponentEnd(MacroNode $node, PhpWriter $writer)
	{
		$tag = $node->htmlNode->name;

		preg_match("#<$tag(?:\[(?<component>[^\]]+)\])?(?:\s+\.\.(?<var>\\\$?[a-zA-Z0-9_]+))?#", $node->content, $m);
		$node->content = preg_replace("#<$tag\[[^\]]+\](\s+\.\.\\\$?[a-zA-Z0-9_]+)?#", "<$tag", $node->content);
		$component     = empty($m['component']) ? $tag : $m['component'];
		$arg           = ":$component";

		$start = '<?php $_children = $_l->children[] = new stdClass; ob_start(); ?>';

		$end = '<?php
			$html = ob_get_contents(); ob_end_clean();
			$_b->templates[%var]->renderChildTemplate(%var, ["_components" => $_components + (array)$_children, "_html" => $html] + %node.array %raw + $template->getParameters());
			array_pop($_l->children); $_children = end($_l->children);
		?>';

		$end = $writer->write($end,
			$this->getCompiler()->getTemplateId(),
			ComponentMacro::componentTemplate($component),
			isset($m['var']) ? (ltrim($m['var'], '$')==='this' ? '+ get_defined_vars()' : "+ (array)$m[var]") : ""
		);

		if (!empty($m['component'])) {
			$start = "\\0$start";
			$end   = "$end\\0";
		}

		foreach (['child', 'key'] as $macro) {
			if (isset($node->htmlNode->macroAttrs[$macro])) {
				if (empty($m['component'])) {
					$node->htmlNode->name = 'Void';
					$start = "<Void>$start";
					$end   = "$end</Void>";
				}

				if (empty($node->htmlNode->macroAttrs[$macro])) {
					$node->htmlNode->macroAttrs[$macro] = $arg;
				}
			}
		}

		if (empty($node->htmlNode->macroAttrs['key']) && empty($node->htmlNode->macroAttrs['child'])) {
			$start = $writer->write('<?php if (!empty($_components[%word])) { echo $_components[%word]; unset($_components[%word]); } else { ?>',
				$arg, $arg, $arg, $arg
			) . $start;

			$end = $end . '<?php } ?>';
		}

		$node->content = preg_replace("#<$tag.*>#U", $start, $node->content, 1);
		$node->content = preg_replace("#</$tag>#", $end, $node->content, 1);
	}



	public function macroChildEnd(MacroNode $node, PhpWriter $writer)
	{
		$tag  = $node->htmlNode->name;
		$keep = ($tag==='Void' ? '' : '\\0');

		$node->content = preg_replace("#<$tag.*>#U", "<?php ob_start() ?>$keep", $node->content, 1);

		$end = $writer->write('$_children->{%word} = trim(ob_get_contents()); ob_end_clean();',
			$node->htmlNode->macroAttrs['child']
		);

		$node->content = preg_replace("#</$tag>#", "$keep<?php $end ?>", $node->content, 1);
	}


	public function macroKeyEnd(MacroNode $node, PhpWriter $writer)
	{
		$tag  = $node->htmlNode->name;
		$arg  = $node->htmlNode->macroAttrs['key'];

		$start = $writer->write('if (!empty($_components[%word])) { echo $_components[%word]; unset($_components[%word]); }',
			$arg, $arg, $arg, $arg
		);

		$node->content = "<?php $start else { ?> {$node->content} <?php } ?>";

		if ($tag==='Void') {
			$node->content = preg_replace("#<$tag.*>#U", '', $node->content, 1);
			$node->content = preg_replace("#</$tag>#", '', $node->content, 1);
		}
	}

}
