<?php

# Parser for PHP source files.
# 
# @package PDoc
class PDoc_Parser
{
  public $basedir;
  public $classes   = array();
  public $functions = array();
  
  static protected $tokens = array();
  
  
  function __construct($basedir)
  {
    $this->basedir = $basedir;
  }
  
  # Adds a source file for parsing.
  function add($filename)
  {
    echo "Parsing: $filename\n";
    
    $contents = file_get_contents($filename);
    $contents = $this->preparse($contents);
    $this->parse(str_replace($this->basedir, '', $filename), $contents);
  }
  
  # IMPROVE: Parse functions/methods' arguments (actually just copied).
  # IMPROVE: Parse interfaces.
  protected function parse($filename, $contents)
  {
    $in_class = false;
    $in_class_deepness = 0;
    $deepness = 0;
    
    foreach(explode("\n", $contents) as $line)
    {
      preg_match("/^(\s*)/", $line, $match);
      $deepness = strlen($match[1]) / 2;
      $trimmed  = trim($line);
      
      if ($deepness == $in_class_deepness
        and $trimmed != '{'
        and $trimmed != '}'
        and $trimmed != '')
      {
        if ($in_class !== false)
        {
          # we are no longer inside a class
          $in_class = false;
          $in_class_deepness = 0;
        }
      }
      
      # class
      if (preg_match('/^\s*(\§comment:[0-9a-z]+\§|)\s*(abstract|)\s*class\s+([\w\_]+)\s*([^\{]+|)/i', $line, $match))
      {
        $klass = $this->parse_class($match);
        $this->classes[$klass['name']] = $klass;
        
        $in_class = $klass['name'];
        $in_class_deepness = $deepness;
      }
      
      # interface
      if (preg_match('/^\s*(\§comment:[0-9a-z]+\§|)\s*()interface\s+([\w\_]+)\s*([^\{]+|)/i', $line, $match))
      {
        $klass = $this->parse_class($match);
        $klass['interface'] = true;
        $this->classes[$klass['name']] = $klass;
        
        $in_class = $klass['name'];
        $in_class_deepness = $deepness;
      }
      
      # function or class method
      elseif (preg_match('/^\s*(\§comment:[0-9a-z]+\§|)([\w\s\&]+?)function\s*&?\s*([^\s\(]*)\s*\((.*)\)\s*;?\s*$/i', $line, $match))
      {
        $func = $this->parse_function($match, $in_class);
        
        if ($in_class === false) {
          $this->functions[$func['name']] = $func;
        }
        else {
          $this->classes[$in_class]['methods'][$func['name']] = $func;
        }
      }
      
      # class attribute
      elseif ($in_class !== false
        and preg_match('/^\s*(\§comment:[0-9a-z]+\§|)\s*((?:public|protected|private|static)\s*(?:public|protected|private|static)?)\s*\$([\w\d_]+)\s*[;=]/i', $line, $match))
      {
        $attribute = $this->parse_class_attribute($match);
        $this->classes[$in_class]['attributes'][$attribute['name']] = $attribute;
      }
    }
  }
  
  private function & parse_class($match)
  {
    $klass = array(
      'abstract'    => ($match[2] == 'abstract') ? true : false,
      'name'        => $match[3],
      'attributes'  => array(),
      'methods'     => array(),
      'brief'       => '',
      'description' => '',
      'params'      => array(),
      'namespace'   => '',
      'interface'   => false,
    );
    
    # class: is documented?
    if (!empty($match[1]))
    {
      $comment = trim(self::$tokens[$match[1]]);
      list($klass['brief'], $klass['description'], $klass['params']) = $this->parse_comment($comment);
    }
    
    # namespace?
    if (isset($klass['params']['namespace'])) {
      $klass['namespace'] = $klass['params']['namespace'];
    }
    elseif (preg_match('/^(.+)_[^_]+$/', $klass['name'], $ns_match)) {
      $klass['namespace'] = $ns_match[1];
    }
    
    # class: inheritance?
    if (preg_match('/extends\s+([\w\_]+)/i', $match[4], $extends))
    {
      $klass['extends'] = $extends[1];
      $match[4] = str_replace($extends[0], '', $match[4]);
    }
    
    # class: implements interfaces?
    if (preg_match('/implements\s+([\w\_\s,]+)/i', $match[4], $interfaces)) {
      $klass['implements'] = array_map('trim', explode(',', $interfaces[1]));
    }
    
    return $klass;
  }
  
  private function & parse_function($match, $is_method)
  {
    $func = array(
      'name'        => $match[3],
      'static'      => (strpos($match[2], 'static') !== false),
      'abstract'    => (strpos($match[2], 'abstract') !== false),
      'visibility'  => 'public',
      'arguments'   => preg_replace('/§comment:([\w\d]+)§/', '', $match[4]),
      'brief'       => '',
      'description' => '',
      'params'      => array(),
      'namespace'   => '',
    );
    
    # function: documented?
    if (!empty($match[1]))
    {
      $comment = trim(self::$tokens[$match[1]]);
      list($func['brief'], $func['description'], $func['params']) = $this->parse_comment($comment);
    }
    
    if ($is_method)
    {
      # function: visibility?
      if (strpos($match[2], 'protected') !== false) {
        $func['visibility'] = 'protected';
      }
      elseif (strpos($match[2], 'private') !== false) {
        $func['visibility'] = 'private';
      }
    }
    else
    {
      # function: namespace?
      if (isset($func['params']['namespace'])) {
        $func['namespace'] = $func['params']['namespace'];
      }
#      elseif (preg_match('/^(.+)_[^_]+$/', $func['name'], $match)) {
#        $func['namespace'] = $match[1];
#      }
    }
    
    # forced private visibility?
    if (isset($func['params']['private'])) {
      $func['visibility'] = 'private';
    }
    
    return $func;
  }
  
  private function & parse_class_attribute($match)
  {
    $attribute = array(
      'name'        => $match[3],
      'static'      => (strpos($match[2], 'static') !== false),
      'visibility'  => 'public',
      'brief'       => '',
      'description' => '',
      'params'      => array(),
    );
    
    # attribute: visibility?
    if (isset($attribute['params']['private'])) {
      $attribute['visibility'] = 'private';
    }
    else
    {
      if (strpos($match[2], 'protected') !== false) {
        $attribute['visibility'] = 'protected';
      }
      elseif (strpos($match[2], 'private') !== false) {
        $attribute['visibility'] = 'private';
      }
    }
    
    # attribute: documented?
    if (!empty($match[1]))
    {
      $comment = trim(self::$tokens[$match[1]]);
      list($attribute['brief'], $attribute['description'], $attribute['params']) = $this->parse_comment($comment);
    }
    
    return $attribute;
  }
  
  # Extracts brief, description and params from a comment.
  protected function parse_comment($comment)
  {
    $comment = preg_replace('/^[ ]/m', '', $comment);
    
    # extracts params
    $params  = array();
    preg_match_all('/^@([^\s]+)\s+(.+)$/m', $comment, $matches, PREG_SET_ORDER);
    foreach($matches as $match) {
      $params[strtolower($match[1])] = $match[2];
    }
    
    # removes params
    $comment = preg_replace('/^@([^\s]+)\s+(.+)$/m', '', $comment);
    
    # distinguishes between brief & description
    $pos = strpos($comment, "\n");
    if ($pos)
    {
      $brief = trim(substr($comment, 0, $pos));
      $description = text_to_html(substr($comment, $pos));
    }
    else
    {
      $brief = $comment;
      $description = '';
    }
    return array($brief, $description, $params);
  }
  
  
  protected function preparse($contents)
  {
    $contents = $this->remove_non_phpcode($contents);
    $contents = $this->tokenize_comments($contents);
    $contents = $this->source_code_beautifier($contents);
    return $contents;
  }
  
  protected function remove_non_phpcode($contents)
  {
    return preg_replace('/(\?\>(.+?)\<\?php|^\s*\<\?php\s*|\s*\?\>\s*$)/s', '', $contents);
  }
  
  protected function tokenize_comments($contents)
  {
    $contents = preg_replace_callback('/\/\*(.+?)\*\//s', array($this, '_replace_comments'), $contents);
    
    $lines   = explode("\n", $contents);
    $comment = '';
    
    foreach($lines as $i => $line)
    {
      if (preg_match('/^\s*(?:#|\/\/)(.*)$/sm', $line, $match))
      {
        $comment .= $match[1]."\n";
        $lines[$i] = "";
      }
      elseif (!empty($comment))
      {
        $token = "§comment:".sha1($comment)."§";
        $comment = preg_replace('/^[ \t]*(?:#|\/\/)/m', '', $comment);
        self::$tokens[$token] = $comment;
        $lines[$i-1] = $token;
        $comment = "";
      }
    }
    $contents = implode(" ", $lines);
    return $contents;
  }
  
  
  protected function source_code_beautifier($contents)
  {
    $contents = preg_replace('/\s+/', ' ', $contents);
    $chars    = preg_split('//', $contents);
    
    $contents  = '';
    $deepness  = 0;
    $line      = '';
    $in_string = false;
    
    foreach($chars as $i => $char)
    {
      # are we starting or closing a string?
      if ($char == '"' or $char == "'")
      {
        if ($in_string === false) {
          $in_string = $char;
        }
        elseif ($in_string == $char and !preg_match('/\\\$/', $line)) {
          $in_string = false;
        }
      }
      
      if ($in_string !== false) {
        $line .= $char;
      }
      else
      {
        # let's indent depending on particular chars:
        switch($char)
        {
          case '{':
            $contents .= (empty($line) ? '' : str_repeat('  ', $deepness)."$line\n").str_repeat('  ', $deepness)."{\n";
            $deepness++;
            $line = '';
            break;
          
          case '}':
            $contents .= (empty($line) ? '' : str_repeat('  ', $deepness)."$line\n").str_repeat('  ', --$deepness)."}\n";
            if ($deepness == 0) {
              $contents .= "\n";
            }
            $line = '';
            break;
          
          case ';':
            $contents .= str_repeat('  ', $deepness)."$line$char\n";
            $line = '';
            break;
          
          case ' ':
            if (empty($line)) {
              break;
            }
          default:
            $line .= $char;
        }
      }
    }
    return $contents;
  }
  
  static protected function _replace_comments($match)
  {
    $token   = "§comment:".sha1($match[0])."§";
    $comment = preg_replace('/^[ \t]*\*/m', '', $match[1]);
    self::$tokens[$token] = $comment;
    return $token;
  }
  
  
  function & filter_instance_methods($methods)
  {
    $rs = array(
      'public'    => array(),
      'protected' => array(),
      'private'   => array(),
    );
    foreach($methods as $method)
    {
      if (!$method['static']) {
        $rs[$method['visibility']][] = $method;
      }
    }
    return $rs;
  }
  
  function & filter_static_methods($methods)
  {
    $rs = array(
      'public'    => array(),
      'protected' => array(),
      'private'   => array(),
    );
    foreach($methods as $method)
    {
      if ($method['static']) {
        $rs[$method['visibility']][] = $method;
      }
    }
    return $rs;
  }
  
  function & filter_static_attributes($attributes)
  {
    $rs = array(
      'public'    => array(),
      'protected' => array(),
      'private'   => array(),
    );
    foreach($attributes as $attribute)
    {
      if ($attribute['static']) {
        $rs[$attribute['visibility']][] = $attribute;
      }
    }
    return $rs;
  }
  
  function & filter_instance_attributes($attributes)
  {
    $rs = array(
      'public'    => array(),
      'protected' => array(),
      'private'   => array(),
    );
    foreach($attributes as $attribute)
    {
      if (!$attribute['static']) {
        $rs[$attribute['visibility']][] = $attribute;
      }
    }
    return $rs;
  }
  
  function & get_tree()
  {
    $rs = array(
      '_global' => array(
        '_functions' => array(),
        '_classes'   => array(),
      ),
    );
    
    foreach($this->classes as $klass)
    {
      if(!empty($klass['namespace']))
      {
        $namespace = explode('_', $klass['namespace']);
        $_rs =& $rs;
        foreach($namespace as $ns)
        {
          if (!isset($_rs[$ns])) {
            $_rs[$ns] = array();
          }
          $_rs =& $_rs[$ns];
        }
        $_rs['_classes'][] = $klass;
      }
      else {
        $rs['_global']['_classes'][] = $klass;
      }
    }
    
    foreach($this->functions as $func)
    {
      if (!empty($func['namespace']))
      {
        $namespace = explode('_', $func['namespace']);
        $_rs =& $rs;
        foreach($namespace as $ns)
        {
          if (!isset($_rs[$ns])) {
            $_rs[$ns] = array();
          }
          $_rs =& $_rs[$ns];
        }
        $_rs['_functions'][] = $func;
      }
      else {
        $rs['_global']['_functions'][] = $func;
      }
    }
    
    return $rs;
  }
}

?>
