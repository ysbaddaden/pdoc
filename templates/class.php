<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Class <?= $klass['name'] ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
<div id="main">

  <header>
    <a href="<?= $this->relative_url() ?>index.html">Documentation for <strong><?= $this->project_name ?></strong></a>
  </header>

  <section id="content">
    <section id="class">
      
      <h1>
        <? if ($klass['abstract']): ?>
          <span class="abstract">Abstract</span>
        <? endif; ?>
        
        <? if ($klass['interface']): ?>
          Interface
        <? else: ?>
          Class
        <? endif; ?>
        
        <span class="name"><?= $klass['name'] ?></span>
      </h1>
      
      <? if (!empty($klass['description'])): ?>
        <div class="description">
          <?= $this->fix_internal_links(text_to_html($klass['description'], array('headings_start' => 2))) ?>
        </div>
      <? endif; ?>
      
      <? if (!empty($klass['extends']) or !empty($klass['implements'])): ?>
        <section id="inheritence">
          <h2>Inheritence</h2>
          
          <dl>
            <? if (!empty($klass['extends'])): ?>
              <dt>Extends:</dt>
              <dd>
                <? if (isset($this->parser->classes[$klass['extends']])): ?>
                  <a href="<?= $this->klass_url($klass['extends']) ?>"><?= $klass['extends'] ?></a>
                <? else: ?>
                  <?= $klass['extends'] ?></a>
                <? endif; ?>
              </dd>
            <? endif; ?>
            
            <? if (!empty($klass['implements'])): ?>
              <dt>Implements:</dt>
              <dd>
                <? foreach($klass['implements'] as $implement): ?>
                  <? if (isset($this->parser->classes[$implement])): ?>
                    <a href="<?= $this->klass_url($implement) ?>"><?= $implement ?></a>,
                  <? else: ?>
                    <a href="http://www.php.net/<?= $implement ?>"><?= $implement ?></a> (PHP),
                  <? endif; ?>
                <? endforeach; ?>
              </dd>
            <? endif; ?>
          </dl>
        </section>
      <? endif; ?>
      
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
            <? if (!$this->document_private and $method['visibility'] == 'private'): ?>
              <? continue; ?>
            <? endif; ?>
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
  
  <aside>
    <? include '_aside.php' ?>
  </aside>
  
  <div class="clear"></div>
</div>
</body>
</html>
