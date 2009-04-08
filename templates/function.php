<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Function: <?= $klass['name'] ?></title>
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
        Function: <?= $function['name'] ?>
      </h1>
      
      <div class="function">
        <h2>
          <span class="name"><?= $function['name'] ?></span>
          <span class="arguments">(<?= $function['arguments'] ?>)</span>
        </h2>
        
        <p class="brief"><?= $function['brief'] ?></p>
        <div class="description"><?= $function['description'] ?></div>
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
