<?php

# Analyzes PHP source files.
# 
# TODO: Parse visibility/static/abstract/final properties for class methods.
# TODO: Parse class attributes (with visibility/static).
# TODO: Parse pseudo-namespaces.
# IMPROVE: Parse namespaces.
# 
class Pdoc_Analyzer
{
  private $tokens;
  private $comment    = '';
  private $properties = array();
  
  private $functions  = array();
  private $classes    = array();
  private $interfaces = array();
  
  
  # Adds a PHP source file to parse.
  function add($php_file)
  {
    $this->tokens     = token_get_all(file_get_contents($php_file));
    $this->comment    = '';
    $this->properties = array();
    
    $token = reset($this->tokens);
    do
    {
      if (!is_string($token))
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
          
          case T_FUNCTION:  $this->parse_function();  break;
          case T_CLASS:     $this->parse_class();     break;
          case T_INTERFACE: $this->parse_interface(); break;
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
  
  
  private function properties()
  {
    $properties = $this->properties;
    $this->properties = array();
    return $properties;
  }
  
  private function comment()
  {
    $comment = $this->comment;
    $this->comment = null;
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
    $this->comment = preg_replace(array('/^\s*(#|[*]) /m', '/\/[*]*\s*|\s*[*]\//'), '', $comment);
  }
  
  private function parse_class()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    
    $name  = $token[1];
    $klass = array_merge(array(
      'comment'    => $this->comment(),
      'extends'    => '',
      'implements' => array(),
      'visibility' => 'public',
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
        
        case T_CONST:
          list($const_name, $const) = $this->parse_class_constant();
          $klass['constants'][$const_name] = $const;
        break;
        
        case T_FUNCTION:
          list($method_name, $method) = $this->parse_class_method();
          $klass['methods'][$method_name] = $method;
        break;
        
        case '}': break 2;
      }
    }
    
    $this->classes[$name] = $klass;
  }
  
  private function parse_class_method()
  {
    list($name, $func) = $this->parse_function_or_method();
#    $func = array_merge($func, $this->properties());
    return array($name, $func);
  }
  
  private function parse_class_constant()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
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
  
  private function parse_interface()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    
    $interface_name = $token[1];
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
    
    $this->interfaces[$interface_name] = $interface;
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
    $this->functions[$name] = $func;
  }
  
  private function parse_function_or_method()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    
    $name = $token[1];
    
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
      $code .= is_string($token) ? $token : $token[1];
      
      switch($token[0])
      {
        case T_COMMENT: case T_DOC_COMMENT: continue;
        case '{': $deep++; break;
        case '}': $deep--; if ($deep < 1) return $code; break;
      }
    }
    return $code;
  }
  
  private function debug_token($token)
  {
    echo is_string($token) ? "CHR: $token\n" :
      token_name($token[0]).": ".str_replace("\n", '', $token[1])."\n";
  }
}

?>
