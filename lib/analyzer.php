<?php
require 'pdoc_klass.php';

# Analyzes PHP source files.
# 
#   $analyzer = new Pdoc_Analyzer();
#   $analyzer->add('src/file.php');
#   $analyzer->add('src/another_file.php');
#   
#   $functions  = $analyzer->functions();
#   $classes    = $analyzer->classes();
# 
# TODO: documentation modifiers => :nodoc:, :doc:, :private:, :namespace:$namespace
# 
class Pdoc_Analyzer
{
  private $tokens;
  private $comment    = '';
  private $properties = array();
  private $namespace  = '';
  
  private $namespaces = array();
  private $classes    = array();
  private $interfaces = array();
  private $functions  = array();
  
  
  # Adds a PHP source file to parse.
  function add($php_file)
  {
#    echo "Analyzing $php_file\n";
    
    $this->tokens     = token_get_all(file_get_contents($php_file));
    $this->comment    = '';
    $this->namespace  = null;
    $this->properties = array();
    
    $token = reset($this->tokens);
    do
    {
      if (!is_string($token))
      {
        switch($token[0])
        {
          case T_COMMENT: case T_DOC_COMMENT: $this->parse_comment(); break;
          case T_ABSTRACT:  $this->properties['abstract'] = true; break;
          case T_FINAL:     $this->properties['final']    = true; break;
          
          case T_FUNCTION:  $this->parse_function();  break;
          case T_CLASS:     $this->parse_class();     break;
          case T_INTERFACE: $this->parse_interface(); break;
          case T_NAMESPACE: $this->parse_namespace(); break;
        }
      }
    }
    while(($token = next($this->tokens)) !== false);
  }
  
  # Returns the list of functions.
  function & functions()
  {
    $functions = $this->functions;
    ksort($functions);
    return $functions;
  }
  
  # Returns the list of classes.
  function & classes()
  {
    $classes = $this->classes;
    ksort($classes);
    return $classes;
  }
  
  # Returns the list of interfaces.
  function & interfaces()
  {
    $interfaces = $this->interfaces;
    ksort($interfaces);
    return $interfaces;
  }
  
  # Returns the list of namespaces.
  function & namespaces()
  {
    $namespaces = $this->namespaces;
    ksort($namespaces);
    return $namespaces;
  }
  
  # Returns the full list of methods and functions.
  function & methods()
  {
    $methods = array();
    
    foreach($this->functions as $func_name => $func)
    {
      $methods[$func_name] = array(
        'visibility' => 'public',
        'path' => $func_name,
      );
    }
    
    foreach($this->classes as $klass_name => $klass)
    {
      foreach($klass['methods'] as $method_name => $method)
      {
        $methods["$method_name ($klass_name)"] = array(
          'visibility' => $method['visibility'],
          'path' => "$klass_name::$method_name",
        );
      }
    }
    
    ksort($methods);
    return $methods;
  }
  
  # Returns true if the class exists in the project.
  function class_exists($klass_name)
  {
    return isset($this->classes[$klass_name]);
  }
  
  # Returns true if the interface exists in the project.
  function interface_exists($interface_name)
  {
    return isset($this->interfaces[$interface_name]);
  }
  
  # Returns true if the function exists in the project.
  function function_exists($function_name)
  {
    return isset($this->functions[$function_name]);
  }
  
  
  private function properties()
  {
    $properties = $this->properties;
    $this->properties = array();
    return $properties;
  }
  
  private function comment()
  {
    $comment = $this->comment;
    $this->comment = '';
    return $comment;
  }
  
  private function parse_comment()
  {
    $token = current($this->tokens);
    $comment = $token[1];
    
    if (preg_match('/^\s*(#|\/\/)/', $comment))
    {
      # searches for a collection of single-line comments
      while(($token = next($this->tokens)) !== false)
      {
        switch($token[0])
        {
          case T_COMMENT: $comment .= $token[1]; break;
          case T_WHITESPACE: if (strpos($token[1], "\n")) return; continue;
          default: break 2;
        }
      }
      prev($this->tokens);
    }
    
    # removes useless chars
    $this->comment = preg_replace(array('/\/[*]+\s*/', '/\s*[*]\//', '/^\s*(#|[*]|\/\/)[ ]?/m'), '', $comment);
  }
  
  
  private function parse_namespace()
  {
    $name = '';
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_WHITESPACE: break;
        case ';': break 2;
        default: $name .= is_string($token) ? $token : $token[1];
      }
    }
    $this->add_namespace($name);
    $this->namespace = $name;
  }
  
  private function parse_class()
  {
    $name = '';
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_WHITESPACE: break;
        case T_NS_SEPARATOR: case T_STRING: $name .= is_string($token) ? $token : $token[1]; break;
        case '{': case T_EXTENDS: case T_IMPLEMENTS: prev($this->tokens); break 2;
      }
    }
    
    $klass = array_merge(array(
      'comment'    => $this->comment(),
      'extends'    => '',
      'implements' => array(),
      'constants'  => array(),
      'attributes' => array(),
      'methods'    => array(),
      'abstract'   => false,
      'final'      => false,
    ), $this->properties());
    
    # definition
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_EXTENDS:
          while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
          $klass['extends'] = $token[1];
        break;
        
        case T_IMPLEMENTS:
        case ',':
          while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
          $klass['implements'][] = $token[1];
        break;
        
        case '{': break 2;
      }
    }
    
    # constants, attributes & methods
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_COMMENT: case T_DOC_COMMENT: $this->parse_comment(); break;
        case T_ABSTRACT:  $this->properties['abstract']   = true; break;
        case T_FINAL:     $this->properties['final']      = true; break;
        case T_STATIC:    $this->properties['static']     = true; break;
        case T_PUBLIC:    $this->properties['visibility'] = 'public';    break;
        case T_PROTECTED: $this->properties['visibility'] = 'protected'; break;
        case T_PRIVATE:   $this->properties['visibility'] = 'private';   break;
        
        case T_CONST:
          list($const_name, $const) = $this->parse_class_constant();
          $klass['constants'][$const_name] = $const;
        break;
        
        case T_VARIABLE:
          list($attribute_name, $attribute) = $this->parse_class_attribute();
          $klass['attributes'][$attribute_name] = $attribute;
        break;
        
        case T_FUNCTION:
          list($method_name, $method) = $this->parse_class_method();
          $klass['methods'][$method_name] = $method;
        break;
        
        case '}': break 2;
      }
    }
    
    if ($this->namespace !== null and strpos($name, '\\') !== 0) {
      $name = $this->namespace.'\\'.$name;
    }
    
    $this->classes[$name] = new Pdoc_Klass($klass);
    
    $_name = strpos($name, '_') ? str_replace('_', '\\', $name) : $name;
    if (strpos($_name, '\\'))
    {
      $namespace = $this->add_namespace_for($_name);
      $this->namespaces[$namespace]['classes'][$name] = $this->classes[$name];
    }
  }
  
  private function parse_class_constant()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    return $this->parse_class_variable();
  }
  
  private function parse_class_variable()
  {
    $token = current($this->tokens);
    $name  = $token[1];
    $value = '';
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case '=': continue;
        case ';': break 2;
        default: $value .= is_string($token) ? $token : $token[1];
      }
    }
    
    $const = array(
      'value' => trim($value),
      'comment' => $this->comment(),
    );
    
    return array($name, $const);
  }
  
  private function parse_class_attribute()
  {
    $defaults = array(
      'visibility' => 'public',
      'static'     => false,
    );
    list($name, $attribute) = $this->parse_class_variable();
    $attribute = array_merge($defaults, $attribute, $this->properties());
    return array($name, $attribute);
  }
  
  private function parse_class_method()
  {
    $defaults = array(
      'visibility' => 'public',
      'abstract'   => false,
      'final'      => false,
      'static'     => false,
    );
    list($name, $func) = $this->parse_function_or_method();
    $func = array_merge($defaults, $func, $this->properties());
    return array($name, $func);
  }
  
  private function parse_interface()
  {
    $name = '';
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_WHITESPACE: break;
        case T_NS_SEPARATOR: case T_STRING: $name .= is_string($token) ? $token : $token[1]; break;
        case '{': case T_EXTENDS: prev($this->tokens); break 2;
      }
    }
    
    $interface = array(
      'comment'   => $this->comment(),
      'extends'   => array(),
      'constants' => array(),
      'methods'   => array(),
    );
    
    # definition
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_EXTENDS:
        case ',':
          while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
          $interface['extends'][] = $token[1];
        break;
        case '{': break 2;
      }
    }
    
    # constants & methods
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_COMMENT: case T_DOC_COMMENT: $this->parse_comment(); break;
        
        case T_CONST:
          list($const_name, $const) = $this->parse_class_constant();
          $interface['constants'][$const_name] = $const;
        break;
        
        case T_FUNCTION:
          list($method_name, $method) = $this->parse_interface_method();
          $interface['methods'][$method_name] = $method;
        break;
        
        case '}': break 2;
      }
    }
    
    if ($this->namespace !== null and strpos($name, '\\') !== 0) {
      $name = $this->namespace.'\\'.$name;
    }

    $this->interfaces[$name] = $interface;
    
    $_name = strpos($name, '_') ? str_replace('_', '\\', $name) : $name;
    if (strpos($_name, '\\'))
    {
      $namespace = $this->add_namespace_for($_name);
      $this->namespaces[$namespace]['interfaces'][$name] =& $this->interfaces[$name];
    }
  }
  
  private function parse_interface_method()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    $name = $token[1];
    
    $method = array(
      'arguments' => $this->parse_function_args(),
      'comment'   => $this->comment(),
    );
    
    return array($name, $method);
  }
  
  
  private function parse_function()
  {
    list($name, $func) = $this->parse_function_or_method();
    
    if ($this->namespace !== null and strpos($name, '\\') !== 0) {
      $name = $this->namespace.'\\'.$name;
    }
    
    $this->functions[$name] = $func;
    
    if (strpos($name, '\\'))
    {
      $namespace = $this->add_namespace_for($name);
      $this->namespaces[$namespace]['functions'][$name] =& $this->functions[$name];
    }
  }
  
  private function parse_function_or_method()
  {
    $name = '';
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_WHITESPACE: break;
        case T_NS_SEPARATOR: case T_STRING: $name .= is_string($token) ? $token : $token[1]; break;
        case '(': prev($this->tokens); break 2;
      }
    }
    
    $func = array(
      'arguments' => $this->parse_function_args(),
      'code'      => $this->parse_function_code(),
      'comment'   => $this->comment(),
    );
    
    return array($name, $func);
  }
  
  private function parse_function_args()
  {
    $args = '';
    $deep = 0;
    
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_WHITESPACE: if ($deep > 0) $args .= ' '; break;
        case '(': if ($deep > 0) $args .= '('; $deep++; break;
        case ')': $deep--; if ($deep == 0) break 2;
        default:
          if ($deep > 0) {
            $args .= is_string($token) ? $token : $token[1];
          }
      }
    }
    return $args;
  }
  
  # IMPROVE: Remove minimal indentation from code.
  private function parse_function_code()
  {
    $code = '';
    $deep = 0;
    
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_COMMENT: case T_DOC_COMMENT: continue;
        
        case T_CURLY_OPEN:
        case '{':
          $deep++;
          if ($deep == 1) {
            $indent = 0;
          }
          break;
        
        case '}':
          $deep--;
          if ($deep < 1)
          {
            $code .= '}';
            break 2;
          }
        break;
      }
      $code .= is_string($token) ? $token : $token[1];
    }
    
    return $code;
  }
  
  private function add_namespace($namespace)
  {
    if (!isset($this->namespaces[$namespace]))
    {
      $this->namespaces[$namespace] = array(
        'comment'    => '',
        'functions'  => '',
        'classes'    => '',
        'interfaces' => '',
      );
    }
  }
  
  private function add_namespace_for($name)
  {
    $parts = explode('\\', $name, -1);
    foreach($parts as $ns)
    {
      $namespace = isset($namespace) ? "$namespace\\$ns" : $ns;
      $this->add_namespace($namespace);
    }
    return $namespace;
  }
  
  private function debug_token($token)
  {
    echo is_string($token) ? "CHR: $token\n" :
      token_name($token[0]).": ".str_replace("\n", '', $token[1])."\n";
  }
}

?>
