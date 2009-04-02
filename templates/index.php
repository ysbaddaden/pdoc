<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="style.css"/>
</head>
<body>
<div id="main">

  <h2>List of classes:</h2>
  
  <ul class="classes">
    <? foreach($classes as $klass): ?>
      <? $resource = "class-{$klass['name']}.html" ?>
      
      <li>
        <a href="<?= $resource ?>"><?= $klass['name'] ?></a>
      </li>
    <? endforeach; ?>
  </ul>
  
  
  <h2>List of global functions:</h2>
  
  <ul class="functions">
    <? foreach($functions as $func): ?>
      <? $resource = "function-{$klass['name']}.html" ?>
      
      <li>
        <a href="<?= $func['name'] ?>"><?= $func['name'] ?></a>
      </li>
    <? endforeach; ?>
  </ul>

</div>
</body>
</html>
