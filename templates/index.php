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

  <h1 class="index">Documentation for <strong><?= $this->project_name ?></strong></h1>
  
  <section id="content">
  <?
  if (file_exists($this->inputdir.'doc/README')) {
    echo $this->fix_internal_links(text_to_html(file_get_contents($this->inputdir.'doc/README')));
  }
  ?>
  </section>
  
  <aside>
    <? include '_aside.php' ?>
  </aside>
  
</div>
</body>
</html>
