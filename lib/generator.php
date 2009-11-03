<?php

# Generates the HTML documentation from parsed source files.
# 
# TODO: render global functions (ie. that do not belong to a namespace) as classes/_global.html
# TODO: render a main_file as index file (within frameset).
# 
class Pdoc_Generator
{
  protected $analyzer;
  protected $project_name;
  
  protected $inputdir;
  protected $outputdir;
  
  protected $relative_url = '';
  
  
  function __construct(Pdoc_Analyzer $analyzer, $options=array())
  {
    $this->analyzer = $analyzer;
    
    foreach($options as $k => $v) {
      $this->$k = $v;
    }
  }
  
  function save()
  {
    $this->render('index',        'index.html');
    $this->render('class_index',  'class_index.html');
    $this->render('method_index', 'method_index.html');
    
    $this->generate_classes();
    $this->generate_interfaces();
#    $this->generate_namespaces();
#    $this->generate_global_namespace();
    
#    $this->render('main', 'readme.html');
    
    /*
    # namespaces
    $tree = $this->parser->get_tree();
    foreach($tree as $ns => $subtree)
    {
      if ($ns != '_classes' and $ns != '_functions') {
        $this->generate_namespace($subtree, $ns);
      }
    }
    */
    
    # CSS
    copy(ROOT.'/templates/style.css', $this->outputdir.'/style.css');
  }
  
  private function generate_classes()
  {
    foreach($this->analyzer->classes() as $klass_name => $klass)
    {
      ksort($klass['constants']);
      ksort($klass['attributes']);
      ksort($klass['methods']);
      
      $this->relative_url(count(explode('_', $klass_name)));
      
      $this->render('class', $this->klass_path($klass_name), array(
        'klass_name' => $klass_name,
        'klass'      => &$klass,
      ));
    }
  }
  
  private function generate_interfaces()
  {
    foreach($this->analyzer->interfaces() as $interface_name => $interface)
    {
      ksort($interface['constants']);
      ksort($interface['methods']);
      
      $this->relative_url(count(explode('_', $interface_name)));
      
      $this->render('interface', $this->interface_path($interface_name), array(
        'interface_name' => $interface_name,
        'interface'      => &$interface,
      ));
    }
  }
  
  private function render($template, $output_file, $locals=array())
  {
    echo "Generating $output_file\n";
    
    # some local vars
    foreach($locals as $k => $v) {
      $$k = $v;
    }
    
    # renders template
    ob_start();
    include ROOT."/templates/{$template}.php";
    $contents = ob_get_clean();
    
    # saves HTML file
    if (!file_exists($this->outputdir.dirname($output_file))) {
      mkdir($this->outputdir.dirname($output_file), 0775, true);
    }
    file_put_contents($this->outputdir.$output_file, $contents);
  }
  
  
  protected function text_to_html($text, $options=array())
  {
    $html = text_to_html($text, $options);
    return preg_replace('/(src|href)="classes\//', '\1="'.$this->relative_url().'classes/', $html);
  }
  
  protected function span_to_html($span, $options=array())
  {
    $html = span_to_html($span, $options);
    return preg_replace('/(src|href)="classes\//', '\1="'.$this->relative_url().'classes/', $html);
  }
  
  
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
  
  protected function namespace_path($namespace)
  {
    return "classes/".str_replace('_', '/', $namespace).".html";
  }
  
  protected function namespace_url($namespace)
  {
    return $this->relative_url().$this->namespace_path($namespace);
  }
  
  protected function klass_path($klass)
  {
    return "classes/".str_replace('_', '/', $klass).'.html';
  }
  
  protected function klass_url($klass)
  {
    return $this->relative_url().$this->klass_path($klass);
  }
  
  protected function interface_path($interface)
  {
    return "interfaces/".str_replace('_', '/', $interface).'.html';
  }
  
  protected function interface_url($interface)
  {
    return $this->relative_url().$this->interface_path($interface);
  }
  
  protected function method_url($method)
  {
    if (strpos($method, '::'))
    {
      list($klass, $func) = explode('::', $method, 2);
      return $this->relative_url().$this->klass_path($klass)."#method-$func";
    }
    else {
      return $this->relative_url()."classes/_global.html#method-$method";
    }
  }
  
  
  protected function filter_class_attributes($data)
  {
    $static   = array();
    $instance = array();
    
    foreach($this->filter($data, 'static', true) as $name => $d) {
      $static[$d['visibility']][$name] = $d;
    }
    foreach($this->filter($data, 'static', false) as $name => $d) {
      $instance[$d['visibility']][$name] = $d;
    }
    
    return array($static, $instance);
  }
  
  protected function filter_class_methods($data)
  {
    return $this->filter_class_attributes($data);
  }
  
  
  private function filter($data, $type, $value)
  {
    $rs = array();
    foreach($data as $name => $d)
    {
      if ($d[$type] === $value) {
        $rs[$name] = $d;
      }
    }
    return $rs;
  }
  
  
  /*
  protected function generate_readme()
  {
    $readme = file_exists($this->inputdir.'/doc/README') ?
      file_get_contents($this->inputdir.'/doc/README') : "";
    
    ob_start(); include ROOT.'/templates/readme.php';
    file_put_contents($this->outputdir.'/readme.html', ob_get_clean());
  }
  
  protected function generate_index()
  {
    ob_start(); include ROOT.'/templates/index.php';
    file_put_contents($this->outputdir.'/index.html', ob_get_clean());
  }
  
  protected function generate_class_index()
  {
    ob_start(); include ROOT.'/templates/class_index.php';
    file_put_contents($this->outputdir.'/class_index.html', ob_get_clean());
  }
  
  protected function generate_method_index()
  {
    ob_start(); include ROOT.'/templates/method_index.php';
    file_put_contents($this->outputdir.'/method_index.html', ob_get_clean());
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
    
    if (!file_exists($this->outputdir.dirname($path))) {
      mkdir($this->outputdir.dirname($path), 0775, true);
    }
    file_put_contents($this->outputdir.$path, $contents);
  }
  */
}

?>
