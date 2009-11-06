<?php

if (version_compare(phpversion(), '5.0', '<')) {
  trigger_error("PHP 5.0+ is required to run pdoc.", E_USER_ERROR);
}

error_reporting(E_ALL);
define('ROOT', dirname(dirname(__FILE__)));

require ROOT.'/lib/simple_markup.php';
require ROOT.'/lib/pdoc/browser.php';
require ROOT.'/lib/pdoc/analyzer.php';
require ROOT.'/lib/pdoc/generator.php';

# params
$excludes         = array();
$project_name     = '';
$document_private = false;
$main_file        = 'README';

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
        echo "  --private        Document private.\n";
        echo "  --main file      Use this file as index page (path is relative to input directory, defaults to README).\n";
      exit;
      
      case 'exclude':
        if ($value === null and isset($_SERVER['argv'][++$n])) {
          $value = $_SERVER['argv'][$n];
        }
        $excludes[] = $value;
      break;
      
      case 'project':
        if ($value === null and isset($_SERVER['argv'][++$n])) {
          $value = $_SERVER['argv'][$n];
        }
        $project_name = $value;
      break;
      
      case 'private':
        $document_private = true;
      break;
      
      case 'main':
        if ($value === null and isset($_SERVER['argv'][++$n])) {
          $value = $_SERVER['argv'][$n];
        }
        $main_file = $value;
      break;
      
      default:
        trigger_error("Unknown parameter: --{$arg}\n");
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
  $basedir = getcwd();
}
if (!isset($outputdir)) {
  $outputdir = rtrim($basedir, '/').'/doc';
}

# searches for source code
echo "Searching... ";
$browser = new Pdoc_Browser($basedir);
$files = $browser->search('php', $excludes);
echo "done.\n";

# parses source code
echo "Analyzing source code... ";
$analyzer = new Pdoc_Analyzer();
foreach($files as $file) {
  $analyzer->add($file);
}
echo "done.\n";

# generates the HTML documentation
echo "Generating documentation... ";
$generator = new Pdoc_Generator($analyzer, array(
  'inputdir'         => $basedir,
  'outputdir'        => $outputdir,
  'project_name'     => $project_name,
  'document_private' => $document_private,
  'main_file'        => $main_file,
));
$generator->save();
echo "done.\n";
echo "\n";

?>
