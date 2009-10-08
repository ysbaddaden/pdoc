<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en"/>
  <title>Documentation for <?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
<div id="main">

  <header>
    <a href="<?= $this->relative_url() ?>index.html">Documentation for <strong><?= $this->project_name ?></strong></a>
  </header>
  
  <section>
    <h1>List of classes</h1>
    
    <ul class="class-index">
      <? foreach($this->parser->classes as $klass): ?>
        <li><a href="<?= $this->klass_url($klass) ?>"><?= $klass['name'] ?></a></li>
      <? endforeach; ?>
    </ul>
  </section>
  
</div>
</body>
</html>
