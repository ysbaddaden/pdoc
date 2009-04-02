<?php

error_reporting(E_ALL);

require dirname(__FILE__).'/browser.php';
require dirname(__FILE__).'/parser.php';
require dirname(__FILE__).'/generator.php';

# params
if ($_SERVER['argc'] == 3)
{
  $basedir   = $_SERVER['argv'][1];
  $outputdir = $_SERVER['argv'][2];
}
elseif ($_SERVER['argc'] == 2)
{
  $basedir   = $_SERVER['argv'][1];
  $outputdir = "$basedir/doc";
}
else
{
  $basedir   = $_ENV['PWD'];
  $outputdir = "$basedir/doc";
}

# searches for source code
$browser = new PDoc_Browser($basedir);
$files = $browser->search('php');

# parses source code
$parser = new PDoc_Parser($basedir);
foreach($files as $file) {
  $parser->add($file);
}
$parser->compute();

# generates the HTML documentation
$generator = new PDoc_Generator($parser);
$generator->save();

?>