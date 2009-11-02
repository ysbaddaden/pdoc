<?php

# Replacement for +Pdoc_Parser+ which uses PHP's tokenizer.
# 
# TODO: Parse interface method declarations (with visibility and arguments).
# TODO: Parse abstract/final properties for classes.
# TODO: Parse class methods (with visibility/static/abstract/final and arguments).
# TODO: Parse class attributes (with visibility/static).
# TODO: Parse pseudo-namespaces.
# IMPROVE: Parse namespaces.
# 
class Pdoc_Analyzer
{
  private $tokens;
  private $comment    = '';
  
  private $functions  = array();
  private $classes    = array();
  private $interfaces = array();
  
  
  # Analyzes a PHP source file for functions, classes, etc.
  function add($php_file)
  {
    $this->tokens = token_get_all(file_get_contents($php_file));
    $token = reset($this->tokens);
    do
    {
      if (!is_string($token))
      {
        switch($token[0])
        {
          case T_COMMENT: case T_DOC_COMMENT: $this->parse_comment(); break;
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
  
  # Returns the list of classes.
  function & interfaces()
  {
    $interfaces = $this->interfaces;
    ksort($interfaces);
    return $interfaces;
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
    $klass = array(
      'comment'    => $this->comment,
      'extends'    => '',
      'implements' => array(),
    );
    $this->comment = '';
    
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
    
    # method declarations
    // ...

    $this->classes[$name] = $klass;
  }
  
  private function parse_interface()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    
    $name = $token[1];
    $interface = array(
      'comment'    => $this->comment,
      'extends'    => array(),
    );
    $this->comment = '';
    
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
    
    # method declarations
    // ...
    
    $this->interfaces[$name] = $interface;
  }

  private function parse_function()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    
    $name = $token[1];
    $args = $this->parse_function_args();
    $code = $this->parse_function_code();
    
    $this->functions[$name] = array('arguments' => $args/*, 'code' => $code*/);
    $this->functions[$name]['comment'] = $this->comment;
    $this->comment = '';
  }
  
  # IMPROVE: parse forced type, like: function b(array $ary);
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
