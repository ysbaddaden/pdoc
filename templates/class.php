<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Class: <?= $klass['name'] ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="style.css"/>
</head>
<body>
<div id="main">

  <p id="header">
    <a href="index.html">Documentation for <strong><?= $this->project_name ?></strong></a>
  </p>

  <div id="content">
    <div id="class">
      
      <h1>
        <? if ($klass['abstract']): ?>
          <span class="abstract">Abstract</span>
        <? endif; ?>
        Class: <?= $klass['name'] ?>
      </h1>
      
      <p class="brief"><?= $klass['brief'] ?></p>
      
      <dl class="inheritence">
        <? if (!empty($klass['extends'])): ?>
          <dt>inherits from:</dt>
          <dd><a href="class-<?= $klass['extends'] ?>.html"><?= $klass['extends'] ?></a></dd>
        <? endif; ?>
        
        <? if (!empty($klass['implements'])): ?>
          <dt>implements:</dt>
          <dd>
            <? foreach($klass['implements'] as $implement): ?>
              <a href="class-<?= $implement ?>.html"><?= $implement ?></a>,
            <? endforeach; ?>
          </dd>
        <? endif; ?>
      </dl>
      
      <div class="description"><?= $klass['description'] ?></div>
      
      
      <h2>Attributes</h2>
      
      <p>TODO</p>
      
      
      <h2>Methods</h2>
      
      <dl class="methods">
      
        <? $public_methods    = $this->parser->filter_methods($klass['methods'], 'public') ?>
        <? $protected_methods = $this->parser->filter_methods($klass['methods'], 'protected') ?>
        <? $private_methods   = $this->parser->filter_methods($klass['methods'], 'private') ?>
      
        <? if (!empty($public_methods)): ?>
          <dt>Public methods:</dt>
          <dd>
            <ul>
              <? foreach($public_methods as $method): ?>
                <? $resource = "#method-{$method['name']}" ?>
                <li><a href="<?= $resource ?>" title="<?= $klass['name'] ?>::<?= $method['name'] ?>(<?= $method['arguments'] ?>)"><?= $method['name'] ?></a></li>
              <? endforeach; ?>
            </ul>
          </dd>
        <? endif; ?>
        
        <? if (!empty($protected_methods)): ?>
          <dt>Protected methods:</dt>
          <dd>
            <ul>
              <? foreach($protected_methods as $method): ?>
                <? $resource = "#method-{$method['name']}" ?>
                <li><a href="<?= $resource ?>" title="<?= $klass['name'] ?>::<?= $method['name'] ?>(<?= $method['arguments'] ?>)"><?= $method['name'] ?></a></li>
              <? endforeach; ?>
            </ul>
          </dd>
        <? endif; ?>
        
        <? if (!empty($private_methods)): ?>
          <dt>Private methods:</dt>
          <dd>
            <ul>
              <? foreach($private_methods as $method): ?>
                <? $resource = "#method-{$method['name']}" ?>
                <li><a href="<?= $resource ?>" title="<?= $klass['name'] ?>::<?= $method['name'] ?>(<?= $method['arguments'] ?>)"><?= $method['name'] ?></a></li>
              <? endforeach; ?>
            </ul>
          </dd>
        <? endif; ?>
      </dl>
      
      <!--h3>Inherited methods:</h3>
      <p>TODO</p-->
      
      
      <div id="methods">
        <? foreach($klass['methods'] as $method): ?>
          <? $resource = "#{$method['name']}" ?>
          
          <div class="method <?= $method['visibility'] ?>" id="method-<?= $method['name'] ?>">
            <h3>
              <span class="visibility"><?= $method['visibility'] ?></span>
              <span class="name"><!--<?= $klass['name'] ?>::--><?= $method['name'] ?></span>
              <span class="arguments">(<?= $method['arguments'] ?>)</span>
            </h3>
            
            <p class="brief"><?= $method['brief'] ?></p>
            <div class="description"><?= $method['description'] ?></div>
          </div>
        <? endforeach; ?>
      </div>
    </div>
  </div>
  
  <hr/>
  
  <div id="navbar">
    <? include '_navbar.php' ?>
  </div>
  
  <div class="clear"></div>
</div>
</body>
</html>
