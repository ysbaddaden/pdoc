<?php
error_reporting(E_ALL);
define('ROOT', dirname(__FILE__));

require ROOT.'/browser.php';
require ROOT.'/parser.php';
require ROOT.'/generator.php';

# params
$excludes = array();

for ($n = 1; $n < $_SERVER['argc']; $n++)
{
  $arg = $_SERVER['argv'][$n];
  if (strpos($arg, '--') === 0)
  {
    $arg = explode('=', ltrim($arg, '-'));
    switch($arg[0])
    {
      case 'exclude':
        $excludes[] = $arg[1];
        break;
      default:
        trigger_error("Unknown parameter: --{$arg[0]}\n");
    }
  }
  elseif (!isset($basedir)) {
    $basedir = $arg;
  }
  else {
    $outputdir = $arg;
  }
}

if (!isset($basedir)) {
  $basedir = $_ENV['PWD'];
}
if (!isset($outputdir)) {
  $outputdir = rtrim($basedir, '/').'/doc';
}

# searches for source code
$browser = new PDoc_Browser($basedir);
$files = $browser->search('php', $excludes);

# parses source code
$parser = new PDoc_Parser($basedir);
foreach($files as $file)
{
  $parser->add($file);
}

# generates the HTML documentation
$generator = new PDoc_Generator($parser);
$generator->save($outputdir);

?>
