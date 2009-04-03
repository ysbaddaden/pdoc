<?php

# Generates the HTML documentation from parsed source files.
class PDoc_Generator
{
  public $parser;
  public $project_name;
  
  function __construct($parser, $project_name)
  {
    $this->project_name = $project_name;
    $this->parser = $parser;
  }
  
  # TODO: Generate documentation for functions.
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
    
    copy(ROOT.'/templates/style.css', $this->outputdir.'/style.css');
  }
  
  protected function generate_index()
  {
#    echo "Generating documentation...\n";
    
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
}

?>
