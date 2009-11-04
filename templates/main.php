<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title><?= $this->project_name ?></title>
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>

<?php
$contents = file_exists($main_file) ?
  file_get_contents($main_file) :
  "=Documentation for {$this->project_name}";

echo $this->text_to_html($contents, array('headings_start' => 1));
?>

</body>
</html>
