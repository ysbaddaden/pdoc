<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title><?= $ns_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
  
  <section id="class">
    <h1>
      <strong><?= $ns_name ?></strong>
    </h1>
    
    <? if (!empty($ns['comment'])): ?>
      <div class="description">
        <?= $this->text_to_html($ns['comment'], array('headings_start' => 2)) ?>
      </div>
    <? endif; ?>
    
    <? if (!empty($ns['classes'])): ?>
      <h2>Classes:</h2>
      
      <ul class="classes">
        <? foreach($ns['classes'] as $klass_name => $klass): ?>
          <? $klass_name = end(explode('\\', $klass_name)) ?>
          <li><a href="<?= $this->klass_url($klass_name) ?>"><?= $klass_name ?></a></li>
        <? endforeach; ?>
      </ul>
    <? endif; ?>
    
    <? if (!empty($ns['functions'])): ?>
      <h2>Functions:</h2>
      
      <ul class="functions">
        <? foreach($ns['functions'] as $func_name => $func): ?>
          <? $func_name = end(explode('\\', $func_name)) ?>
          <li><a href="#function-<?= $func_name ?>"><?= $func_name ?></a></li>
        <? endforeach; ?>
      </ul>
      
      <section class="functions">
        <? foreach($ns['functions'] as $func_name => $func): ?>
          <? $func_name = end(explode('\\', $func_name)) ?>
          <? include('_function.php') ?>
        <? endforeach; ?>
      </section>
    <? endif; ?>
  </section>

</body>
</html>
