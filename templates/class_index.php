<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Classes for <?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body class="index">

  <h1>List of classes</h1>
  
  <? $collection = array_merge($this->analyzer->classes(), $this->analyzer->interfaces(), $this->analyzer->namespaces()) ?>
  <? ksort($collection) ?>
  
  <ul class="index">
    <? foreach($collection as $klass_name => $klass): ?>
      <li><a href="<?= $this->klass_url($klass_name) ?>" target="docwin"><?= $klass_name ?></a></li>
    <? endforeach; ?>
  </ul>

</body>
</html>
