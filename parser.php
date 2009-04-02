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
    
    // ...
    
    print_r(preg_replace('/\s+/', ' ', $contents)."\n\n");
  }
  
  protected function preparse($contents)
  {
    # replaces comments with tokens
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
    
    # removes strings
    $contents = preg_replace('/\'.*?\'/', '', $contents);
    $contents = preg_replace('/".*?"/',   '', $contents);
    
    return $contents;
  }
  
  static protected function _replace_comments($match)
  {
    $token   = "§comment:".sha1($match[0])."§";
    $comment = preg_replace('/^\s*\*/m', '', $match[1]);
    self::$tokens[$token] = $comment;
    return $token;
  }
  
  # Computes parsed source files,
  # organising files, classes, methods, etc.
  function compute()
  {
    
  }
}

?>
