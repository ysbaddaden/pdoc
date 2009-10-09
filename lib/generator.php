<?php

# Generates the HTML documentation from parsed source files.
class PDoc_Generator
{
  public $parser;
  public $project_name;
  
  public $inputdir;
  public $outputdir;
  
  public $relative_url = '';
  
  function __construct($parser, $project_name, $document_private=false)
  {
    $this->project_name     = $project_name;
    $this->document_private = $document_private;
    $this->parser           = $parser;
  }
  
  function save($inputdir, $outputdir)
  {
    $this->inputdir  = $inputdir;
    $this->outputdir = $outputdir;
    
    if (!is_dir($this->outputdir)) {
      mkdir($this->outputdir, 0755, true);
    }
    
    ksort($this->parser->classes);
    ksort($this->parser->functions);
    
    # indexes
    $this->generate_index();
#    $this->generate_class_index();
#    $this->generate_method_index();
    
    # each class
    foreach($this->parser->classes as $klass) {
      $this->generate_class($klass);
    }
    
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
  /*
  protected function generate_class_index()
  {
    ob_start();
    include ROOT.'/templates/class_index.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/class_index.html', $contents);
  }
  
  protected function generate_methods_index()
  {
    ob_start();
    include ROOT.'/templates/method_index.php';
    $contents = ob_get_clean();
    
    file_put_contents($this->outputdir.'/method_index.html', $contents);
  }
  */
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
    
    if ($namespace == '_global') {
      $this->relative_url(1);
    }
    else {
      $this->relative_url(count(explode('_', $namespace)));
    }
    
    ob_start();
    include ROOT.'/templates/namespace.php';
    $contents = ob_get_clean();
    
    $path = $this->namespace_path($namespace);
    @mkdir($this->outputdir.dirname($path), 0775, true);
    file_put_contents($this->outputdir.$path, $contents);
    
    # child namespaces
    foreach($tree as $ns => $subtree) {
      $this->generate_namespace($subtree, $namespace.'_'.$ns);
    }
  }
  
  protected function generate_class($klass)
  {
    echo "Generating documentation for class: {$klass['name']} ({$klass['namespace']})\n";
    
    ksort($klass['methods']);
    $this->relative_url(count(explode('_', $klass['name'])));
    
    ob_start();
    include ROOT.'/templates/class.php';
    $contents = ob_get_clean();
    
    $path = $this->klass_path($klass);
    @mkdir($this->outputdir.dirname($path), 0775, true);
    file_put_contents($this->outputdir.$path, $contents);
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
  
  protected function stylesheet_url()
  {
    return $this->relative_url().'style.css';
  }
  
  protected function relative_url($deepness=null)
  {
    if ($deepness !== null) {
      $this->relative_url = str_repeat('../', $deepness);
    }
    return $this->relative_url;
  }
  
  protected function klass_path($klass)
  {
    $name = is_array($klass) ? $klass['name'] : $klass;
    return "classes/".str_replace('_', '/', $name).'.html';
  }
  
  protected function namespace_path($namespace)
  {
    return "classes/".str_replace('_', '/', $namespace).".html";
  }
  
  protected function klass_url($klass)
  {
    return $this->relative_url().$this->klass_path($klass);
  }
  
  protected function namespace_url($namespace)
  {
    return $this->relative_url().$this->namespace_path($namespace);
  }
  
  protected function render_tree($tree, $namespace='')
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
          $klass_url  = $this->klass_url($klass);
          if ($klass_name != 'NS') {
            echo "<dd class=\"klass\"><a title=\"Class: {$klass['name']}\" href=\"{$klass_url}\">{$klass_name}</a></dd>\n";
          }
        }
      }
      /*
      elseif ($ns == '_functions')
      {
        ksort($subtree);
        foreach($subtree as $func)
        {
          $func_name = empty($namespace) ? $func['name'] : str_replace("{$namespace}_", '', $func['name']);
          echo "<dd class=\"func\" title=\"Function: {$func['name']}\">{$func_name}</dd>\n";
        }
      }
      */
      elseif ($ns != '_functions')
      {
        $ns_name = empty($namespace) ? $ns : $namespace.'_'.$ns;
        $ns_url  = $this->namespace_url($ns_name);
        echo "<dt><a href=\"{$ns_url}\">$ns</a></dt>\n";
        echo "<dd>";
        $this->render_tree($subtree, $ns_name);
        echo "</dd>";
      }
    }
    echo "</dl>\n";
  }
  
  protected function fix_internal_links($html)
  {
    return preg_replace('/(src|href)="classes\//', '\1="'.$this->relative_url().'classes/', $html);
  }
}

?>
