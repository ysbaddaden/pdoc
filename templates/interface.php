<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Interface <?= $klass_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
  
  <section id="class">
    <h1>Interface <strong><?= $interface_name ?></strong></h1>
    
    <? if (!empty($interface['comment'])): ?>
      <div class="description">
        <?= $this->text_to_html($klass['comment'], array('headings_start' => 2)) ?>
      </div>
    <? endif; ?>
    
    <? if (!empty($klass['extends'])): ?>
      <section id="inheritence">
        <h2>Inheritence</h2>
        
        <dl>
          <? if (!empty($klass['extends'])): ?>
            <dt>Extends:</dt>
            <dd>
              <? foreach($klass['extends'] as $implement): ?>
                <? if ($this->analyzer->interface_exists($implement))): ?>
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
    
    <? if (!empty($interface['constants'])): ?>
      <section id="constants">
        <h2>Constants</h2>
        <? $constants = $interface['constants'] ?>
        <? include('_constants.php') ?>
      </section>
    <? endif; ?>
    
    <? if (!empty($klass['methods'])): ?>
      <section id="methods">
        <h2>Methods</h2>
        
        <ul class="methods">
          <? foreach($interface['methods'] as $method_name => $method): ?>
            <? if ($method['visibility'] != 'private' or $this->document_private): ?>
              <li>
                <a href="#method-<?= $method_name ?>" title="<?= $method_name ?>(<?= $method['arguments'] ?>)">
                  <?= $method_name ?>
                </a>
              </li>
            <? endif; ?>
          <? endforeach; ?>
        </ul>
        
        <? if (!empty($interface['methods'])): ?>
          <h2>Methods</h2>
          <? $methods = $interface['methods'] ?>
          <? include('_methods.php') ?>
        <? endif; ?>
      </section>
    <? endif; ?>
    
  </section>
  
</body>
</html>
