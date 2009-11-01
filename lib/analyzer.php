<?php

# Replacement for +Pdoc_Parser+ which uses PHP's tokenizer.
# 
# IMPROVE: Parse classes and interfaces (with abstract/final).
# IMPROVE: Parse class methods (with visibility/static/abstract/final).
# IMPROVE: Parse class attributes (with visibility/static).
class Pdoc_Analyzer
{
  private $tokens;
  private $comment   = '';
  
  private $functions = array();
  
  
  # Analyzes a PHP source file for functions, classes, methods, etc.
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
          case T_FUNCTION: $this->parse_function(); break;
          case T_COMMENT: case T_DOC_COMMENT: $this->parse_comment(); break;
#          case T_CLASS:    $this->parse_class();    break;
#          case T_METHOD:   $this->parse_method();   break;
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
  
  private function parse_function_args()
  {
    $args = array();
    
    while(($token = next($this->tokens)) !== false)
    {
      switch($token[0])
      {
        case T_VARIABLE: $var = $token[1]; break;
        case '=': $args[] = "$var=".$this->parse_function_arg_value(); unset($var); break;
        case ',': if (isset($var)) $args[] = $var; break;
        case ')': if (isset($var)) $args[] = $var; break 2;
      }
    }
    return implode(', ', $args);
  }
  
  private function parse_function_arg_value()
  {
    $val  = '';
    $deep = 0;
    
    while(($token = next($this->tokens)) !== false)
    {
      if (($token[0] == ',' and $deep < 1)
        or ($token[0] == ')' and $deep == 0))
      {
        prev($this->tokens);
        return $val;
      }
      
      $val .= is_string($token) ? $token : $token[1];
      switch($token[0])
      {
        case '(': $deep++; break;
        case ')': $deep--; if ($deep < 1) return $val; break;
      }
    }
    return $val;
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
