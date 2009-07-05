<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Documentation for <?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="style.css"/>
</head>
<body>
<div id="main">

  <section id="index">
    <h1 class="index">Documentation for <strong><?= $this->project_name ?></strong></h1>
    
    <!--h2>List of classes:</h2-->
    
    <? $tree = $this->parser->get_tree() ?>
    
    <dl class="tree">
      <? foreach($tree['packages'] as $package => $data): ?>
        <dt><?= $package ?></dt>
        <dd>
          <? if (!empty($data['classes'])): ?>
            <ul>
              <? foreach($data['classes'] as $klass): ?>
                <? $resource = "class-{$klass['name']}.html" ?>
                <li><a href="<?= $resource ?>"><?= $klass['name'] ?></a></li>
              <? endforeach; ?>
            </ul>
          <? endif; ?>
          
          <? if (!empty($data['subpackages'])): ?>
            <dl>
              <? foreach($data['subpackages'] as $subpackage => $classes): ?>
                <dt><?= $subpackage ?></dt>
                <dd>
                  <ul>
                    <? foreach($classes as $klass): ?>
                      <? $resource = "class-{$klass['name']}.html" ?>
                      <li><a href="<?= $resource ?>"><?= $klass['name'] ?></a></li>
                    <? endforeach; ?>
                  </ul>
                </dd>
              <? endforeach; ?>
            </dl>
          <? endif; ?>
        </dd>
      <? endforeach; ?>
      
      <? if (!empty($tree['classes'])): ?>
        <dt>Global</dt>
        <dd>
          <ul>
            <? foreach($tree['classes'] as $klass): ?>
              <? $resource = "class-{$klass['name']}.html" ?>
              <li><a href="<?= $resource ?>"><?= $klass['name'] ?></a></li>
            <? endforeach; ?>
          </ul>
        </dd>
      <? endif; ?>
    </dl>
  </section>
  
  <!--ul class="classes">
    <? foreach($this->parser->classes as $klass): ?>
      <? $resource = "class-{$klass['name']}.html" ?>
      <li><a href="<?= $resource ?>"><?= $klass['name'] ?></a></li>
    <? endforeach; ?>
  </ul>
  
  
  <h2>List of global functions:</h2>
  
  <ul class="functions">
    <? foreach($this->parser->functions as $func): ?>
      <? $resource = "function-{$klass['name']}.html" ?>
      <li><a href="<?= $func['name'] ?>"><?= $func['name'] ?></a></li>
    <? endforeach; ?>
  </ul-->

</div>
</body>
</html>
