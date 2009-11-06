<?php

# Generates the HTML documentation from parsed source files.
class Pdoc_Generator
{
  protected $analyzer;
  protected $project_name;
  protected $main_file;
  
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
    $this->render('method_index', 'method_index.html');
    
    $this->generate_class_index();
    
    $this->generate_classes();
    $this->generate_interfaces();
    $this->generate_namespaces();
    $this->generate_global_namespace();
    
    $this->relative_url(0);
    $this->render('main', 'readme.html', array('main_file' => $this->inputdir.'/'.$this->main_file));
    
    copy(ROOT.'/templates/style.css', $this->outputdir.'/style.css');
  }
  
  
  private function generate_class_index()
  {
    $collection = array_merge($this->analyzer->classes(), $this->analyzer->interfaces(), $this->analyzer->namespaces());
    ksort($collection);
    $this->render('class_index',  'class_index.html', array('collection' => &$collection));
  }
  
  private function generate_classes()
  {
    foreach($this->analyzer->classes() as $klass_name => $klass)
    {
      $klass_name = ltrim($klass_name, '\\');
      $this->relative_url(count(preg_split('/(_|\\\)/', $klass_name)));
      
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
  
  private function generate_namespaces()
  {
    foreach($this->analyzer->namespaces() as $ns_name => $ns)
    {
      $ns_name = ltrim($ns_name, '\\');
      $this->relative_url(count(explode('\\', $ns_name)));
      
      $this->render('namespace', $this->namespace_path($ns_name), array(
        'ns_name' => $ns_name,
        'ns'      => &$ns,
      ));
    }
  }
  
  private function generate_global_namespace()
  {
    $this->relative_url(1);
    $this->render('global_namespace', 'classes/_global.html');
  }
  
  
  private function render($template, $output_file, $locals=array())
  {
    #echo "Generating $output_file\n";
    
    # some local vars
    foreach($locals as $k => $v) {
      $$k = $v;
    }
    
    # renders template
    ob_start();
    include ROOT."/templates/{$template}.php";
    $contents = ob_get_clean();
    
    # saves HTML file
    $dir = $this->outputdir.'/'.dirname($output_file);
    if (!file_exists($dir)) {
      mkdir($dir, 0775, true);
    }
    file_put_contents($this->outputdir.'/'.$output_file, $contents);
  }
  
  
  # See <tt>SimpleMarkup</tt> for help.
  protected function text_to_html($text, $options=array())
  {
    $html = text_to_html($text, $options);
    return preg_replace('/(src|href)="classes\//', '\1="'.$this->relative_url().'classes/', $html);
  }
  
  # See <tt>SimpleMarkup</tt> for help.
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
    return "classes/".ltrim(str_replace(array('_', '\\'), '/', $namespace), '/').".html";
  }
  
  protected function namespace_url($namespace)
  {
    return $this->relative_url().$this->namespace_path($namespace);
  }
  
  protected function klass_path($klass)
  {
    return "classes/".ltrim(str_replace(array('_', '\\'), '/', $klass), '/').'.html';
  }
  
  protected function klass_url($klass)
  {
    return $this->relative_url().$this->klass_path($klass);
  }
  
  protected function interface_path($interface)
  {
    return $this->klass_path($interface);
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
}

?>
