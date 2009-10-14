<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Classes for <?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body class="index">

  <h1>List of classes</h1>
  
  <ul class="index">
    <? foreach($this->parser->list_classes() as $aside_name => $aside_klass): ?>
      <? if (!isset($aside_klass['visibility']) or $aside_klass['visibility'] != 'private'): ?>
        <li><a href="<?= $this->klass_url($aside_name) ?>" target="docwin"><?= $aside_name ?></a></li>
      <? endif; ?>
    <? endforeach; ?>
  </ul>

</body>
</html>
