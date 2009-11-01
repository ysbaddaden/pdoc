<?php

# Replacement for +Pdoc_Parser+ which uses PHP's tokenizer.
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
        list($id, $text, $line) = $token;
        
        $this->debug_token($token);
        
        switch($id)
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
    $this->comment = $token[1];
    
    if (preg_match('/^\s*(#|\/\/)/', $this->comment))
    {
      while(($token = next($this->tokens)) !== false)
      {
        switch($token[0])
        {
          case T_COMMENT: $this->comment .= $token[1]; break;
          case T_WHITESPACE: continue;
          default: break 2;
        }
      }
      prev($this->tokens);
    }
    $this->comment = preg_replace(array('/^\s*(#|[*]) /m', '/\/[*]*\s*|\s*[*]\//'), '', $this->comment);
  }
  
  private function parse_function()
  {
    while(($token = next($this->tokens)) !== false and $token[0] != T_STRING) continue;
    
    $name = $token[1];
    $args = $this->parse_function_args();
    $this->functions[$name] = array('arguments' => $args);
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
  
  private function debug_token($token)
  {
    echo is_string($token) ? "CHR: $token\n" :
      token_name($token[0]).": ".str_replace("\n", '', $token[1])."\n";
  }
}

?>
