<?php

# Parser for PHP source files.
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
  
  # TODO: Extract class methods.
  # TODO: Extract class attributes.
  # TODO: Extract functions.
  # IMPROVE: Extract interfaces.
  protected function parse($filename, $contents)
  {
    foreach(explode("\n", $contents) as $line)
    {
      if (preg_match('/(\§comment:[0-9a-z]+\§|)\s*(abstract|)\s*class\s+([\w\_]+)\s*([^\{]+|)/si', $line, $match))
      {
        $klass = array(
          'file'     => $filename,
          'comment'  => empty($match[1]) ? "" : trim(self::$tokens[$match[1]]),
          'abstract' => ($match[2] == 'abstract') ? true : false,
          'name'     => $match[3],
        );
        
        if (preg_match('/extends\s+([\w\_]+)/i', $match[4], $extends))
        {
          $klass['extends'] = $extends[1];
          $match[4] = str_replace($extends[0], '', $match[4]);
        }
        
        if (preg_match('/implements\s+([\w\_\s,]+)/i', $match[4], $interfaces)) {
          $klass['implements'] = array_map('trim', explode(',', $interfaces[1]));
        }
        
        $this->classes[$klass['name']] = $klass;
      }
    }
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
      if (preg_match('/^\s*(?:#|\/\/)(.+)$/m', $line, $match))
      {
        $comment .= $match[1]."\n";
        $lines[$i] = "";
      }
      elseif (!empty($comment))
      {
        $token   = "§comment:".sha1($comment)."§";
        $comment = preg_replace('/^\s*(?:#|\/\/)/m', '', $comment);
        
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
    $comment = preg_replace('/^\s*\*/m', '', $match[1]);
    self::$tokens[$token] = $comment;
    return $token;
  }
}

?>
