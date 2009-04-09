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
  
  # TODO: Extract class attributes.
  # TODO: Parse functions/methods' parameters.
  # IMPROVE: Extract interfaces.
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
      
      if ($deepness == $in_class_deepness and $trimmed != '{' and $trimmed != '}' and $trimmed != '')
      {
        if ($in_class !== false)
        {
          # we are no longer inside a class
          $in_class = false;
          $in_class_deepness = 0;
        }
      }
      
      # CLASS
      if (preg_match('/^\s*(\§comment:[0-9a-z]+\§|)\s*(abstract|)\s*class\s+([\w\_]+)\s*([^\{]+|)/i', $line, $match))
      {
        $klass = array(
          'file'     => $filename,
          'abstract' => ($match[2] == 'abstract') ? true : false,
          'name'     => $match[3],
          'methods'  => array(),
          'brief'    => '',
          'description' => '',
          'params' => array(),
        );
        
        if (!empty($match[1]))
        {
          $comment = trim(self::$tokens[$match[1]]);
          list($klass['brief'], $klass['description'], $klass['params']) = $this->parse_comment($comment);
        }
        
        if (preg_match('/extends\s+([\w\_]+)/i', $match[4], $extends))
        {
          $klass['extends'] = $extends[1];
          $match[4] = str_replace($extends[0], '', $match[4]);
        }
        
        if (preg_match('/implements\s+([\w\_\s,]+)/i', $match[4], $interfaces)) {
          $klass['implements'] = array_map('trim', explode(',', $interfaces[1]));
        }
        
        $this->classes[$klass['name']] = $klass;
        
        $in_class = $klass['name'];
        $in_class_deepness = $deepness;
      }
      
      # FUNCTION
      if (preg_match('/^\s*(\§comment:[0-9a-z]+\§|)([\w\s]+?)function\s*([^\s\(]*)\s*\((.*)\)\s*$/i', $line, $match))
      {
        $func = array(
          'file'      => $filename,
          'name'      => $match[3],
          'static'    => (strpos($match[2], 'static') !== false),
          'abstract'  => (strpos($match[2], 'abstract') !== false),
          'arguments' => $match[4],
          'brief'     => '',
          'description' => '',
          'params'    => array(),
        );
        
        if (!empty($match[1]))
        {
          $comment = trim(self::$tokens[$match[1]]);
          list($func['brief'], $func['description'], $func['params']) = $this->parse_comment($comment);
        }
        
        if (strpos($match[2], 'protected') !== false) {
          $func['visibility'] = 'protected';
        }
         elseif (strpos($match[2], 'private') !== false) {
          $func['visibility'] = 'private';
        }
        else {
          $func['visibility'] = 'public';
        }
        
        if ($in_class === false) {
          $this->functions[$func['name']] = $func;
        }
        else {
          $this->classes[$in_class]['methods'][$func['name']] = $func;
        }
      }
    }
  }
  
  # FIXME: If comment has a code part with empty lines, it breaks the code in parts (while it shouldn't).
  protected function parse_comment($comment)
  {
    $comment = preg_replace('/^[ ]/m', '', $comment);
    
    # extracts params
    $params  = array();
    preg_match_all('/^@([^\s]+)\s+(.+)$/m', $comment, $matches, PREG_SET_ORDER);
    foreach($matches as $match) {
      $params[strtolower($match[1])] = $match[2];
    }
#   $comment = preg_replace('/^[ \t]*\@(\w+)\s+(.+)$/m', $comment);

    # distinguishes brief & description's contents
    $parts = explode("\n\n", $comment);
    $brief = array_shift($parts);
    
    $description = '';
    foreach($parts as $part)
    {
      $part = preg_replace('/\$[\w]+/', '<span class="variable">\0</span>', $part);
      if (preg_match('/^\s*[\-\*]\s+/', $part)) {
        $part = '<ul>'.preg_replace('/\s*[\-\*]\s+(.+)$/m', '<li>\1</li>', $part).'</ul>';
      }
      $part = str_replace("<code>\n", '<code>', $part);
      $description .= "<p>$part</p>";
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
  
  
  # Returns from the given methods, the ones that are public, private or protected.
  function & filter_methods($methods, $visibility='public')
  {
    $rs = array();
    foreach($methods as $method)
    {
      if ($method['visibility'] == $visibility) {
        $rs[] = $method;
      }
    }
    return $rs;
  }
  
  function & get_tree()
  {
    $rs = array(
      'packages' => array(),
      'classes'  => array()
    );
    
    foreach($this->classes as $klass)
    {
      if (isset($klass['params']['package']))
      {
        $package = $klass['params']['package'];
        if (!isset($rs['packages'][$package]))
        {
          $rs['packages'][$package] = array(
            'subpackages' => array(),
            'classes'     => array(),
          );
        }
        
        if (isset($klass['params']['subpackage']))
        {
          $subpackage = $klass['params']['subpackage'];
          if (!isset($rs['packages'][$package])) {
            $rs['packages'][$package]['subpackages'][$subpackage] = array();
          }
          $rs['packages'][$package]['subpackages'][$subpackage][] = $klass;
        }
        else {
          $rs['packages'][$package]['classes'][] = $klass;
        }
      }
      else {
        $rs['classes'][] = $klass;
      }
    }
    
    ksort($rs['packages']);
    foreach(array_keys($rs['packages']) as $package)
    {
      ksort($rs['classes']);
      ksort($rs['packages'][$package]);
      ksort($rs['packages'][$package]['subpackages']);
      foreach(array_keys($rs['packages'][$package]['subpackages']) as $subpackage) {
        ksort($rs['packages'][$package]['subpackages'][$subpackage]);
      }
    }
    
    return $rs;
  }
}

?>
