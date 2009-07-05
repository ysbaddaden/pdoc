<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Class <?= $klass['name'] ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="style.css"/>
</head>
<body>
<div id="main">

  <header>
    <a href="index.html">Documentation for <strong><?= $this->project_name ?></strong></a>
  </header>

  <section id="content">
    <section id="class">
      
      <h1>
        <? if ($klass['abstract']): ?>
          <span class="abstract">Abstract</span>
        <? endif; ?>
        Class
        <span class="name"><?= $klass['name'] ?></span>
      </h1>
      
      <p class="brief"><?= $klass['brief'] ?></p>
      
      <dl class="inheritence">
        <? if (!empty($klass['extends'])): ?>
          <dt>inherits from:</dt>
          <dd><a href="class-<?= $klass['extends'] ?>.html"><?= $klass['extends'] ?></a></dd>
        <? endif; ?>
        
        <? if (!empty($klass['implements'])): ?>
          <dt>implements:</dt>
          <dd>
            <? foreach($klass['implements'] as $implement): ?>
              <a href="class-<?= $implement ?>.html"><?= $implement ?></a>,
            <? endforeach; ?>
          </dd>
        <? endif; ?>
      </dl>
      
      <div class="description"><?= $klass['description'] ?></div>
      
      <section id="attributes">
        <? $static_attributes   = $this->parser->filter_static_attributes($klass['attributes']) ?>
        <? $instance_attributes = $this->parser->filter_instance_attributes($klass['attributes']) ?>
        
        <? if (!empty($static_attributes['public'])): ?>
          <h2>Public static attributes</h2>
          <? $attributes = $static_attributes['public'] ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if (!empty($static_attributes['protected'])): ?>
          <h2>Protected static attributes</h2>
          <? $attributes = $static_attributes['protected'] ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and !empty($static_attributes['private'])): ?>
          <h2>Private static attributes</h2>
          <? $attributes = $static_attributes['private'] ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        
        <? if (!empty($instance_attributes['public'])): ?>
          <h2>Public instance attributes</h2>
          <? $attributes = $instance_attributes['public'] ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if (!empty($instance_attributes['protected'])): ?>
          <h2>Protected instance attributes</h2>
          <? $attributes = $instance_attributes['protected'] ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and !empty($instance_attributes['private'])): ?>
          <h2>Private instance attributes</h2>
          <? $attributes = $instance_attributes['private'] ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
      </section>
      
      
      <section id="methods">
        <h2>Methods</h2>
        
        <ul class="methods">
          <? foreach($klass['methods'] as $method): ?>
            <li>
              <a href="#method-<?= $method['name'] ?>"
                title="<?= $method['visibility'] ?><?= $method['static'] ? ' static' : '' ?> <?= $method['name'] ?>(<?= $method['arguments'] ?>)"
              ><?= $method['name'] ?></a>
            </li>
          <? endforeach; ?>
        </ul>
        
        <? $static_methods   = $this->parser->filter_static_methods($klass['methods']) ?>
        <? $instance_methods = $this->parser->filter_instance_methods($klass['methods']) ?>
        
        <? if (!empty($static_methods['public'])): ?>
          <h2>Public static methods</h2>
          <? $methods = $static_methods['public'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if (!empty($static_methods['protected'])): ?>
          <h2>Protected static methods</h2>
          <? $methods = $static_methods['protected'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and !empty($static_methods['private'])): ?>
          <h2>Private static methods</h2>
          <? $methods = $static_methods['private'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        
        <? if (!empty($instance_methods['public'])): ?>
          <h2>Public instance methods</h2>
          <? $methods = $instance_methods['public'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if (!empty($instance_methods['protected'])): ?>
          <h2>Protected instance methods</h2>
          <? $methods = $instance_methods['protected'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and !empty($instance_methods['private'])): ?>
          <h2>Private instance methods</h2>
          <? $methods = $instance_methods['private'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
      </section>
      
    </section>
  </section>
  
  <hr/>
  
  <nav>
    <? include '_navbar.php' ?>
  </nav>
  
  <div class="clear"></div>
</div>
</body>
</html>
