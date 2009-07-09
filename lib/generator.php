<?php

# Generates the HTML documentation from parsed source files.
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
    
    # index page
    $this->generate_index();
    
    # each class
    foreach($this->parser->classes as $klass) {
      $this->generate_class($klass);
    }
    
    # each function (deprecated)
#    foreach($this->parser->functions as $function) {
#      $this->generate_function($function);
#    }
    
    # each namespace
    $tree = $this->parser->get_tree();
    foreach($tree as $ns => $subtree)
    {
      if ($ns != '_classes' and $ns != '_functions') {
        $this->generate_namespace($subtree, $ns);
      }
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
  
  protected function generate_namespace($tree, $namespace)
  {
    echo "Generating documentation for namespace: {$namespace}\n";
    
    ksort($tree);
    $classes   = isset($tree['_classes'])   ? $tree['_classes']   : array();
    $functions = isset($tree['_functions']) ? $tree['_functions'] : array();
    
    unset($tree['_classes']);
    unset($tree['_functions']);
    
    $comment = null;
    foreach(array_keys($classes) as $i)
    {
      if ($classes[$i]['name'] == "{$namespace}_NS")
      {
        $comment = $classes[$i];
        break;
      }
    }
    
    ob_start();
    include ROOT.'/templates/namespace.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir."/namespace-$namespace.html", $contents);
    
    # child namespaces
    foreach($tree as $ns => $subtree) {
      $this->generate_namespace($subtree, $namespace.'_'.$ns);
    }
  }
  
  protected function generate_class($klass)
  {
    echo "Generating documentation for class: {$klass['name']} ({$klass['namespace']})\n";
    
    ksort($klass['methods']);
    
    ob_start();
    include ROOT.'/templates/class.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/class-'.$klass['name'].'.html', $contents);
  }
  
#  protected function generate_function($function)
#  {
#    echo "Generating documentation for function: {$function['name']} ({$function['namespace']})\n";
#    
#    ob_start();
#    include ROOT.'/templates/function.php';
#    $contents = ob_get_clean();
#    
#    file_put_contents($this->outputdir.'/function-'.$function['name'].'.html', $contents);
#  }
}

function PDoc_render_tree($tree, $namespace='')
{
  ksort($tree);
  
  echo "<dl>\n";
  foreach($tree as $ns => $subtree)
  {
    if ($ns == '_classes')
    {
      ksort($subtree);
      foreach($subtree as $klass)
      {
        $klass_name = empty($namespace) ? $klass['name'] : str_replace("{$namespace}_", '', $klass['name']);
        if ($klass_name != 'NS') {
          echo "<dd class=\"klass\"><a title=\"Class: {$klass['name']}\" href=\"class-{$klass['name']}.html\">{$klass_name}</a></dd>\n";
        }
      }
    }
    elseif ($ns == '_functions')
    {
      ksort($subtree);
      foreach($subtree as $func)
      {
        $func_name = empty($namespace) ? $func['name'] : str_replace("{$namespace}_", '', $func['name']);
        echo "<dd class=\"func\" title=\"Function: {$func['name']}\">{$func_name}</dd>\n";
      }
    }
    else
    {
      $ns_name = empty($namespace) ? $ns : $namespace.'_'.$ns;
      echo "<dt><a href=\"namespace-{$ns_name}.html\">$ns</a></dt>\n";
      echo "<dd>";
      PDoc_render_tree($subtree, $ns_name);
      echo "</dd>";
    }
  }
  echo "</dl>\n";
}

?>
