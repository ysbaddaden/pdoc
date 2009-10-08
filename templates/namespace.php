<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title><?= $namespace ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
<div id="main">
  
  <header>
    <a href="<?= $this->relative_url() ?>index.html">Documentation for <strong><?= $this->project_name ?></strong></a>
  </header>
  
  <section id="content">
    <section id="namespace">
      
      <h1>
        <span class="name"><?= $namespace ?></span>
      </h1>
      
      <? if (!empty($comment)): ?>
        <p class="brief"><?= $comment['brief'] ?></p>
        <p class="description"><?= $comment['description'] ?></p>
     <? endif; ?> 
      
      <? if (!empty($tree)): ?>
        <h2>Namespaces:</h2>
        
        <ul class="classes">
          <? foreach(array_keys($tree) as $_ns): ?>
            <li>
              <a href="<?= $this->namespace_url($namespace.'_'.$_ns) ?>"><?= $namespace.'_'.$_ns ?></a>
            </li>
          <? endforeach; ?>
        </ul>
      <? endif; ?>
      
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
  </section>
  
  <hr/>
  
  <aside>
    <? include '_aside.php' ?>
  </aside>
  
  <div class="clear"></div>
</div>
</body>
</html>
