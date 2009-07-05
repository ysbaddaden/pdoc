<?php

# Generates the HTML documentation from parsed source files.
# @package PDoc
class PDoc_Generator
{
  public $parser;
  public $project_name;
  
  function __construct($parser, $project_name, $document_private=false)
  {
    $this->project_name     = $project_name;
    $this->document_private = $document_private;
    $this->parser           = $parser;
  }
  
  function save($outputdir)
  {
    $this->outputdir = $outputdir;
    
    if (!is_dir($this->outputdir)) {
      mkdir($this->outputdir, 0755, true);
    }
    
    ksort($this->parser->classes);
    ksort($this->parser->functions);
    
    $this->generate_index();
    
    foreach($this->parser->classes as $klass) {
      $this->generate_class($klass);
    }
    
    foreach($this->parser->functions as $function) {
      $this->generate_function($function);
    }
    
    copy(ROOT.'/templates/style.css', $this->outputdir.'/style.css');
  }
  
  protected function generate_index()
  {
    ob_start();
    include ROOT.'/templates/index.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/index.html', $contents);
  }
  
  protected function generate_class($klass)
  {
    echo "Generating documentation for {$klass['name']}\n";
    
    ksort($klass['methods']);
    
    ob_start();
    include ROOT.'/templates/class.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/class-'.$klass['name'].'.html', $contents);
  }
  
  protected function generate_function($function)
  {
    echo "Generating documentation for {$function['name']}\n";
    
    ob_start();
    include ROOT.'/templates/function.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/function-'.$function['name'].'.html', $contents);
  }

}

?>
