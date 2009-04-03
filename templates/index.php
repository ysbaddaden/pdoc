<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Documentation for <?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="style.css"/>
</head>
<body>
<div id="main">

  <h1 class="index">Documentation for <strong><?= $this->project_name ?></strong></h1>
  
  <h2>List of classes:</h2>
  
  <? $tree = $this->parser->get_tree() ?>
  
  <ul>
    <? foreach($tree as $package => $classes): ?>
      <li>
        <? if (is_numeric($package)): ?>
          <? $resource = "class-{$classes['name']}.html" ?>
          <a href="<?= $resource ?>"><?= $classes['name'] ?></a>
        <? else: ?>
          <?= $package ?>
          <ul>
            <? foreach($classes as $klass): ?>
              <? $resource = "class-{$klass['name']}.html" ?>
              <li><a href="<?= $resource ?>"><?= $klass['name'] ?></a></li>
            <? endforeach; ?>
          </ul>
        <? endif; ?>
      </li>
    <? endforeach; ?>
  </ul>
  
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
