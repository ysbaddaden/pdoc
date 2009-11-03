<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Methods for <?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body class="index">

  <h1>List of methods</h1>

  <ul>
    <? foreach($this->analyzer->methods() as $func_name => $func): ?>
      <? if ($func['visibility'] != 'private' or $this->document_private): ?>
        <li><a href="<?= $this->relative_url().$this->method_url($func['path']) ?>" target="docwin"><?= $func_name ?></a></li>
      <? endif; ?>
    <? endforeach; ?>
  </ul>

</body>
</html>
