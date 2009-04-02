<?php

# Generates the HTML documentation from parsed source files.
class PDoc_Generator
{
  protected $parser;
  
  function __construct($parser)
  {
    $this->parser = $parser;
  }
  
  function save($outputdir)
  {
    $this->outputdir = $outputdir;
    
    ksort($this->parser->classes);
    ksort($this->parser->functions);
    
    $this->generate_index();
    
    foreach($this->parser->classes as $klass) {
      $this->generate_class($klass);
    }
  }
  
  protected function generate_index()
  {
    $classes   = $this->parser->classes;
    $functions = $this->parser->functions;
    
    ob_start();
    include 'templates/index.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/index.html', $contents);
  }
  
  protected function generate_class($klass)
  {
    ksort($klass['methods']);
    $classes   = $this->parser->classes;
    $functions = $this->parser->functions;
    
    ob_start();
    include  'templates/class.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/class-'.$klass['name'].'.html', $contents);
  }
}

?>
