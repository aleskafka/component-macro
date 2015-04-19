component-macro
============

Extends Latte with simple component model inspired by JSX for React

### Installation

Use composer:

```bash
$ composer require aleskafka/component-macro
```

Install in configuration file to Latte Engine:

```php
$latte = new Latte\Engine;
ComponentMacro::install($latte, __DIR__ . '/components');
```

### Basic example

Create Header component as latte template in ```__DIR__ . '/components/header.latte```

```latte
{default title=>'', subtitle=>'', follow=>NULL}
<h1>{$title}<small n:if="$subtitle">{$subtitle}</small>
  {if $follow}
  <a href="?follow=0"><span class="fa fa-ok"></span> followed</a>
  {else if $follow!==NULL}
  <a href="?follow=1"><span class="fa fa-add"></span> follow</a>  
  {/if}
</h1>
```

In arbitrary page template render Header component with specific arguments.

```latte
<div class="page">
  <Header n:component="title=>$page->title"></Header>
  
  <div class="page-body"></div>
</div>
```

### Features

For more example see tests. In short you can:

  - render components in React style
  - You can use variable $_html in component. It contains body rendered in parent template inside component tags
  - You can expand variable into component ```<Header ..$page n:component />```
  - You can expand defined variables into component ```<Header ..$this n:component />```
  - You can name elements/components inside component with macro n:key="string" (keys can be dynamic) and override it inside parental component tags:
  
```latte
<Header n:component="...">
  <span n:child="string">Override span[string] subcomponent</span>
</Header>
```

### License

MIT. See full [license](license.md).
