<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Class <?= $klass_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
  
  <section id="class">
    <h1>
      <? if ($klass['abstract']): ?><span class="abstract">Abstract</span><? endif; ?>
      Class <strong><?= $klass_name ?></strong>
    </h1>
    
    <? if (!empty($klass['comment'])): ?>
      <div class="description">
        <?= $this->text_to_html($klass['comment'], array('headings_start' => 2)) ?>
      </div>
    <? endif; ?>
    
    <? if (!empty($klass['extends']) or !empty($klass['implements'])): ?>
      <section id="inheritence">
        <h2>Inheritence</h2>
        
        <dl>
          <? if (!empty($klass['extends'])): ?>
            <dt>Extends:</dt>
            <dd>
              <? if (isset($this->analyzer->class_exists[$klass['extends']])): ?>
                <a href="<?= $this->klass_url($klass['extends']) ?>"><?= $klass['extends'] ?></a>
              <? else: ?>
                <?= $klass['extends'] ?>
              <? endif; ?>
            </dd>
          <? endif; ?>
          
          <? if (!empty($klass['implements'])): ?>
            <dt>Implements:</dt>
            <dd>
              <? foreach($klass['implements'] as $implement): ?>
                <? if (isset($this->analyzer->interface_exists[$implement])): ?>
                  <a href="<?= $this->interface_url($implement) ?>"><?= $implement ?></a>,
                <? else: ?>
                  <?= $implement ?>
                <? endif; ?>
              <? endforeach; ?>
            </dd>
          <? endif; ?>
        </dl>
      </section>
    <? endif; ?>
    
    <? if ($klass->has_constants()): ?>
      <section id="constants">
        <h2>Constants</h2>
        <? $constants = $klass->constants() ?>
        <? include('_constants.php') ?>
      </section>
    <? endif; ?>
    
    <? if ($klass->has_attributes()): ?>
      <section id="attributes">
        <? if ($klass->has_attributes(array('static' => true, 'visibility' => 'public'))): ?>
          <h2>Public static attributes</h2>
          <? $attributes = $klass->attributes(array('static' => true, 'visibility' => 'public')) ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if ($klass->has_attributes(array('static' => true, 'visibility' => 'protected'))): ?>
          <h2>Protected static attributes</h2>
          <? $attributes = $klass->attributes(array('static' => true, 'visibility' => 'protected')) ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and $klass->has_attributes(array('static' => true, 'visibility' => 'private'))): ?>
          <h2>Private static attributes</h2>
          <? $attributes = $klass->attributes(array('static' => true, 'visibility' => 'private')) ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        
        <? if ($klass->has_attributes(array('static' => false, 'visibility' => 'public'))): ?>
          <h2>Public instance attributes</h2>
          <? $attributes = $klass->attributes(array('static' => false, 'visibility' => 'public')) ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if ($klass->has_attributes(array('static' => false, 'visibility' => 'protected'))): ?>
          <h2>Protected instance attributes</h2>
          <? $attributes = $klass->attributes(array('static' => false, 'visibility' => 'protected')) ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and $klass->has_attributes(array('static' => false, 'visibility' => 'private'))): ?>
          <h2>Private instance attributes</h2>
          <? $attributes = $klass->attributes(array('static' => false, 'visibility' => 'private')) ?>
          <? include('_attributes.php') ?>
        <? endif; ?>
      </section>
    <? endif; ?>
    
    <? if ($klass->has_methods()): ?>
      <section id="methods">
        <h2>Methods</h2>
        
        <ul class="methods">
          <? foreach($klass->methods() as $method_name => $method): ?>
            <? if ($method['visibility'] != 'private' or $this->document_private): ?>
              <li>
                <a href="#method-<?= $method_name ?>"
                  title="<?= $method['visibility'] ?><?= $method['static'] ? ' static' : '' ?> <?= $method_name ?>(<?= $method['arguments'] ?>)"
                ><?= $method_name ?></a>
              </li>
            <? endif; ?>
          <? endforeach; ?>
        </ul>
        
        <? if ($klass->has_methods(array('static' => true, 'visibility' => 'public'))): ?>
          <h2>Public static methods</h2>
          <? $methods = $klass->methods(array('static' => true, 'visibility' => 'public')) ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if ($klass->has_methods(array('static' => true, 'visibility' => 'protected'))): ?>
          <h2>Protected static methods</h2>
          <? $methods = $klass->methods(array('static' => true, 'visibility' => 'protected')) ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and $klass->has_methods(array('static' => true, 'visibility' => 'private'))): ?>
          <h2>Private static methods</h2>
          <? $methods = $klass->methods(array('static' => true, 'visibility' => 'private')) ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        
        <? if ($klass->has_methods(array('static' => false, 'visibility' => 'public'))): ?>
          <h2>Public instance methods</h2>
          <? $methods = $klass->methods(array('static' => false, 'visibility' => 'public')) ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if ($klass->has_methods(array('static' => false, 'visibility' => 'protected'))): ?>
          <h2>Protected instance methods</h2>
          <? $methods = $klass->methods(array('static' => false, 'visibility' => 'protected')) ?>
          <? include('_methods.php') ?>
        <? endif; ?>
        
        <? if ($this->document_private and $klass->has_methods(array('static' => false, 'visibility' => 'private'))): ?>
          <h2>Private instance methods</h2>
          <? $methods = $klass->methods(array('static' => false, 'visibility' => 'private')) ?>
          <? include('_methods.php') ?>
        <? endif; ?>
      </section>
    <? endif; ?>
    
  </section>
  
</body>
</html>
