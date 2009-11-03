<?php
error_reporting(E_ALL);
define('ROOT', dirname(dirname(__FILE__)));

require ROOT.'/lib/browser.php';
require ROOT.'/lib/text_parser.php';
require ROOT.'/lib/analyzer.php';
require ROOT.'/lib/generator.php';

# params
$excludes         = array();
$project_name     = '';
$document_private = false;

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
#        echo "  --private        Document private methods and attributes (defaults: no).\n";
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
  $basedir = $_ENV['PWD'];
}
if (!isset($outputdir)) {
  $outputdir = rtrim($basedir, '/').'/doc';
}


# searches for source code
$browser = new Pdoc_Browser($basedir);
$files = $browser->search('php', $excludes);

# parses source code
$analyzer = new Pdoc_Analyzer();
foreach($files as $file) {
  $analyzer->add($file);
}

# generates the HTML documentation
$generator = new Pdoc_Generator($analyzer, array(
  'inputdir'         => $basedir,
  'outputdir'        => $outputdir,
  'project_name'     => $project_name,
  'document_private' => $document_private,
));
$generator->save();

?>
