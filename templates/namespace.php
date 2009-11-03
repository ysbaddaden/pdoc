<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title><?= $namespace ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
  
  <section id="class">
    <h1>
      <strong><?= $namespace ?></strong>
    </h1>
    
    <? if (!empty($namespace['comment'])): ?>
      <div class="description">
        <?= $this->text_to_html($namespace['comment'], array('headings_start' => 2)) ?>
      </div>
    <? endif; ?>
    
    <? /*if (!empty($tree)): ?>
      <h2>Namespaces:</h2>
      
      <ul class="classes">
        <? foreach(array_keys($tree) as $_ns): ?>
          <li>
            <a href="<?= $this->namespace_url($namespace.'_'.$_ns) ?>"><?= $namespace.'_'.$_ns ?></a>
          </li>
        <? endforeach; ?>
      </ul>
    <? endif;*/ ?>
    
    <? if (!empty($classes)): ?>
      <h2>Classes:</h2>
      
      <ul class="classes">
        <? foreach($classes as $klass): ?>
          <li>
            <a href="<?= $this->klass_url($klass) ?>"><?= $klass['name'] ?></a>
          </li>
        <? endforeach; ?>
      </ul>
    <? endif; ?>
    
    <? if (!empty($functions)): ?>
      <h2>Functions:</h2>
      
      <ul class="functions">
        <? foreach($functions as $function): ?>
          <li>
            <a href="#function-<?= $function['name'] ?>"><?= $function['name'] ?></a>
          </li>
        <? endforeach; ?>
      </ul>
      
      <section class="functions">
        <? foreach($functions as $function): ?>
          <? include('_function.php') ?>
        <? endforeach; ?>
      </section>
    <? endif; ?>
  </section>

</body>
</html>
