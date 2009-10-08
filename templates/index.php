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

  <section id="index">
    <h1 class="index">Documentation for <strong><?= $this->project_name ?></strong></h1>
    
    <div class="tree">
      <? $tree = $this->parser->get_tree() ?>
      <? $this->render_tree($tree) ?>
    </div>
  </section>
  
</div>
</body>
</html>
