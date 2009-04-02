<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Class: <?= $klass['name'] ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="style.css"/>
</head>
<body>
<div id="main">
  
  <div id="content">
    <h1>Class: <?= $klass['name'] ?></h1>
    
    <ul class="about">
      <? if ($klass['abstract']): ?>
        <li>class is <strong>abstract</strong></li>
      <? endif; ?>
      
      <? if (!empty($klass['extends'])): ?>
        <li>
          inherits from:
            <a href="class-<?= $klass['extends'] ?>.html"><?= $klass['extends'] ?></a>
        </li>
      <? endif; ?>
      
      <? if (!empty($klass['implements'])): ?>
        <li>
          implements:
          <? foreach($klass['implements'] as $implement): ?>
            <a href="class-<?= $implement ?>.html"><?= $implement ?></a>,
          <? endforeach; ?>
        </li>
      <? endif; ?>
    </ul>


    <h2>List of attributes:</h2>
    
    <p>TODO</p>
    
    
    <h2>List of methods: <?= $klass['name'] ?></h2>
    
    <ul class="methods">
      <? foreach($klass['methods'] as $method): ?>
        <? $resource = "#{$method['name']}" ?>
        <li>
          <a href="<?= $resource ?>"><?= $method['name'] ?></a>(<?= $method['params']?>)
        </li>
      <? endforeach; ?>
    </ul>
    
    <h3>Inherited methods:</h3>
    
    <p>TODO</p>
    
    
    <h2>Methods</h2>
    
    <div id="methods">
      <? foreach($klass['methods'] as $method): ?>
        <?
        $resource = "#{$method['name']}";
        $about    = preg_split("/[\n\.]/", $method['comment'], PREG_SPLIT_NO_EMPTY);
        $brief    = array_shift($about);
        $about    = substr($method['comment'], strlen($brief));
        ?>
        
        <div class="method">
          <h3>
            <span class="name"><!--<?= $klass['name'] ?>::--><?= $method['name'] ?></span>
            <span class="params">(<?= $method['params'] ?>)</span></h3>
          <p class="brief"><?= $brief ?></p>
          
          <div class="about">
            <?= str_replace("\n", '<br/>', $about) ?>
          </div>
        </div>
      <? endforeach; ?>
    </div>
    
    
  </div>
  
  
  <div id="navbar">
    <dl class="classes">
      <dt>Classes:</dt>
      
      <? foreach($classes as $_klass): ?>
        <? $resource = "class-{$_klass['name']}.html" ?>
        <dd>
          <a href="<?= $resource ?>"><?= $_klass['name'] ?></a>
        </dd>
      <? endforeach; ?>
    </dl>
  </div>
  
  <div class="clear"></div>
</div>
</body>
</html>
