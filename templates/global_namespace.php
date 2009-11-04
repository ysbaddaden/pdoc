<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>
  
  <h2>Functions:</h2>
  
  <ul class="functions">
    <? foreach($this->analyzer->functions() as $func_name => $func): ?>
      <? if (strpos($func_name, '\\') === false): ?>
        <li><a href="#function-<?= $func_name ?>"><?= $func_name ?></a></li>
      <? endif; ?>
    <? endforeach; ?>
  </ul>
  
  <section class="functions">
    <? foreach($this->analyzer->functions() as $func_name => $func): ?>
      <? if (strpos($func_name, '\\') === false): ?>
        <? include('_function.php') ?>
      <? endif; ?>
    <? endforeach; ?>
  </section>

</body>
</html>
