<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>Documentation for <?= $this->project_name ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" charset="utf-8" href="<?= $this->stylesheet_url() ?>"/>
</head>
<body>

  <h1>Documentation for <span class="name"><?= $this->project_name ?></span></h1>
  
  <? echo $this->fix_internal_links(text_to_html($readme)) ?>

</body>
</html>
