<?php
error_reporting(E_ALL);
define('ROOT', dirname(dirname(__FILE__)));

require ROOT.'/lib/browser.php';
require ROOT.'/lib/text_parser.php';
require ROOT.'/lib/parser.php';
require ROOT.'/lib/generator.php';

# params
$excludes = array();
$project  = '';

# parses params
for ($n = 1; $n < $_SERVER['argc']; $n++)
{
  $arg = $_SERVER['argv'][$n];
  if (strpos($arg, '--') === 0)
  {
    $arg = ltrim($arg, '-');
    if (strpos($arg, '=')) {
      list($arg, $value) = explode('=', $arg);
    }
    elseif (isset($_SERVER['argv'][++$n])) {
      $value = $_SERVER['argv'][$n];
    }
    else {
      $value = null;
    }
    
    switch($arg)
    {
      case 'help': 
        echo "Usage: pdoc [options] [input_directory] [output_directory]\n";
        echo "Options:\n";
        echo "  --help           Displays this help message.\n";
        echo "  --project name   Sets the project's name.\n";
        echo "  --exclude path/  Excludes a file or directory from documentation.\n";
      break;
      case 'exclude': $excludes[] = $value; break;
      case 'project': $project = $value; break;
      default: trigger_error("Unknown parameter: --{$arg}\n");
    }
  }
  elseif (!isset($basedir)) {
    $basedir = $arg;
  }
  else {
    $outputdir = $arg;
  }
}

# default params (when missing)
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
foreach($files as $file) {
  $parser->add($file);
}

# generates the HTML documentation
$generator = new PDoc_Generator($parser, $project);
$generator->save($outputdir);

?>
