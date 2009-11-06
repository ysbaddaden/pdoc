<?php

function text_to_html($text, $options=null)
{
  $parser = new SimpleMarkup($text, $options);
  return $parser->transform();
}

function span_to_html($block)
{
  $parser = new SimpleMarkup('');
  return $parser->parse_span($block);
}

function syntax_highlight($code)
{
  return SimpleMarkup::syntax_highlight($code);
}


class SimpleMarkup
{
  public $text;
  public $options = array(
    'pre_width'        => 2,
    'headings_start'   => 2,
    'syntax_highlight' => false,
  );
  
  private static $keywords  = array(T_ABSTRACT, T_ARRAY, T_AS, T_BREAK, T_CASE, T_CATCH, T_CLASS, T_CLONE,
    T_CONST, T_DECLARE, T_DEFAULT, T_DO, T_ECHO, T_ELSE, T_ELSEIF, T_EMPTY, T_ENDDECLARE,
    T_ENDFOR, T_ENDFOREACH, T_ENDIF, T_ENDSWITCH, T_ENDWHILE, T_EVAL, T_EXIT, T_EXTENDS,
    T_FINAL, T_FOR, T_FOREACH, T_FUNCTION, T_GLOBAL, T_GOTO, T_IF, T_IMPLEMENTS, T_INCLUDE,
    T_INCLUDE_ONCE, T_INSTANCEOF, T_INTERFACE, T_ISSET, T_LIST, T_LOGICAL_AND, T_LOGICAL_OR,
    T_LOGICAL_XOR, T_NEW, T_PRINT, T_PRIVATE, T_PUBLIC, T_PROTECTED, T_REQUIRE, T_REQUIRE_ONCE,
    T_RETURN, T_STATIC, T_SWITCH, T_THROW, T_TRY, T_UNSET, T_USE, T_VAR, T_WHILE, 
  );
  private static $comments = array(T_COMMENT, T_DOC_COMMENT);
  private static $numbers  = array(T_DNUMBER, T_LNUMBER, T_NUM_STRING);
  private static $casts    = array(T_ARRAY_CAST, T_BOOL_CAST, T_DOUBLE_CAST, T_DOUBLE_COLON, T_INT_CAST,
    T_OBJECT_CAST, T_STRING_CAST, T_UNSET_CAST);
  private static $vars     = array(/*T_DOLLAR_CURLY_OPEN, T_CURLY_OPEN,*/ T_VARIABLE, T_STRING_VARNAME, T_VARIABLE);
  private static $tags     = array(T_CLOSE_TAG, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO);
  private static $strings  = array(T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE);
  
  
  function __construct($text, $options=null)
  {
    if (!empty($options)) {
      $this->options = array_merge($this->options, $options);
    }
    $this->text = trim($text);
    $this->linearize_text();
  }
  
  # Transforms plain text to html.
  function transform()
  {
    $blocks = explode("\n\n", $this->text);
    $html   = '';
    
    foreach($blocks as $block)
    {
      if (empty($block)) {
        continue;
      }
      $indent = $this->get_indentation($block);
      
      if ($indent >= $this->options['pre_width'])
      {
        # preformated text
        $block = str_replace(array('<\?', '?\>'), array('<?', '?>'), $block);
        $block = trim(preg_replace("/\n[ ]{".$indent."}/", "\n", $block));
        $block = $this->options['syntax_highlight'] ? syntax_highlight($block) : htmlspecialchars($block);
        $html .= "<pre>$block</pre>\n";
      }
      elseif(preg_match('/^([=]+)(.+?)[=]*$/s', $block, $m))
      {
        # heading
        $hx    = strlen($m[1]) - 1 + $this->options['headings_start'];
        $html .= "<h{$hx}>".trim($m[2])."</h{$hx}>\n";
      }
      elseif(preg_match('/^[\-\*] /s', $block))
      {
        # unordered list
        $block = $this->parse_unordered_list($block);
        $html .= "<ul>".$block."</ul>\n";
      }
      elseif(preg_match('/^\d+. /s', $block))
      {
        # ordered list
        $block = $this->parse_ordered_list($block);
        $html .= "<ol>".$block."</ol>\n";
      }
      elseif(preg_match('/^\[.+?\] /', $block))
      {
        # definition list
        $block = $this->parse_definition_list($block);
        $html .= "<dl>".$block."</dl>\n";
      }
      else
      {
        # paragraph
        $html .= "<p>".$this->parse_span($block)."</p>\n";
      }
    }
    return $html;
  }
  
  # Transforms a block (ie. a single phrase) to HTML.
  function parse_span($block)
  {
    $block = preg_replace_callback('/([+*])(.+?)\1/', array($this, '_replace_span'), $block);
    $block = preg_replace_callback('/<tt>(.+?)<\/tt>/', array($this, '_replace_links'), $block);
    $block = preg_replace_callback('/(?:http|https|ftp|sftp|ssh):\/\/[^ ]+/', array($this, '_auto_links'), $block);
    return $block;
  }
  
  static function syntax_highlight($code)
  {
    if (strpos($code, '<?') === false)
    {
      $code   = "<?\n$code ?>";
      $tokens = token_get_all($code);
      $tokens = array_slice($tokens, 1, -1);
    }
    else {
      $tokens = token_get_all($code);
    }
    
    $html  = '';
    $token = current($tokens);
    do
    {
      $str = is_string($token) ? $token : $token[1];
      
      if (in_array($token[0], self::$tags)) {
        $html .= '<span class="php_tag">'.htmlspecialchars($str).'</span>';
      }
      elseif ($token === "'" or $token === '"')
      {
        $html .= '<span class="php_string">&quot;';
        while(($token = next($tokens)) !== false)
        {
          $str = is_string($token) ? $token : $token[1];
          
          if (in_array($token[0], self::$vars)) {
            $html .= '<span class="php_var">'.$str.'</span>';
          }
          elseif ($token === '"') break;
          else {
            $html .= $str;
          }
        }
        $html .= '&quot;</span>';
      }
      elseif (in_array($token[0], self::$keywords)) {
        $html .= '<span class="php_keyword">'.$str.'</span>';
      }
      elseif (in_array($token[0], self::$comments)) {
        $html .= '<span class="php_comment">'.htmlspecialchars($str).'</span>';
      }
      elseif (in_array($token[0], self::$numbers)) {
        $html .= '<span class="php_number">'.$str.'</span>';
      }
      elseif (in_array($token[0], self::$casts)) {
        $html .= '<span class="php_cast">'.$str.'</span>';
      }
      elseif (in_array($token[0], self::$vars)) {
        $html .= '<span class="php_var">'.$str.'</span>';
      }
      elseif (in_array($token[0], self::$strings)) {
        $html .= '<span class="php_string">'.htmlspecialchars($str).'</span>';
      }
      else {
        $html .= htmlspecialchars($str);
      }
    }
    while(($token = next($tokens)) !== false);
    
    return $html;
  }

  # :nodoc:
  static private function _replace_span($match)
  {
    switch($match[1])
    {
      case '+': return '<code>'.$match[2].'</code>'; break;
      case '*': return '<strong>'.$match[2].'</strong>'; break;
    }
  }
  
  # :nodoc:
  static private function _replace_links($match)
  {
    if (strtolower($match[1]) != $match[1])
    {
      if (strpos($match[1], '::'))
      {
        list($klass, $method) = explode('::', $match[1], 2);
        return '<a href="classes/'.implode('/', explode('_', $klass)).'.html#method-'.str_replace(array('(', ')'), '', $method).'">'.$match[1].'</a>';
      }
      return '<a href="classes/'.implode('/', explode('_', $match[1])).'.html">'.$match[1].'</a>';
    }
    return '<a href="#method-'.str_replace(array('(', ')'), '', $match[1]).'">'.$match[1].'</a>';
  }
  
  # :nodoc:
  static private function _auto_links($match)
  {
    return '<a href="'.htmlspecialchars($match[0]).'">'.$match[0].'</a>';
  }
  
  # Parses an unordered list.
  protected function parse_unordered_list($block)
  {
    $block = "\n$block\n";
    preg_match_all('/\n[\-\*] (.+)/', $block, $matches, PREG_SET_ORDER);
    
    $html = '';
    foreach($matches as $match) {
      $html .= "<li>".$this->parse_span($match[1])."</li>\n";
    }
    return $html;
  }
  
  # Parses an ordered list.
  protected function parse_ordered_list($block)
  {
    $block = "\n$block\n";
    preg_match_all('/\n\d+. (.+)/', $block, $matches, PREG_SET_ORDER);
    
    $html = '';
    foreach($matches as $match) {
      $html .= "<li>".$this->parse_span($match[1])."</li>\n";
    }
    return $html;
  }
  
  # Parses a definition list.
  protected function parse_definition_list($block)
  {
    $block = "\n$block\n";
    preg_match_all('/\n\[(.+?)\] (.+)/', $block, $matches, PREG_SET_ORDER);
    
    $html = '';
    foreach($matches as $match)
    {
      $html .= "<dt>".$this->parse_span($match[1])."</dt>\n";
      $html .= "<dd>".$this->parse_span($match[2])."</dd>\n";
    }
    return $html;
  }
  
  # Beautifies text.
  # 
  # It works by linearizing plain text, which consist of removing
  # useless characters and creating blocks.
  private function linearize_text()
  {
    # cleanup
    $text = str_replace("\r", '', $this->text);
    $text = preg_replace('/\n+\s*\n+/m', "\n\n", $text);
    
    # separates blocks (separator is an empty line)
    $blocks = explode("\n\n", "\n\n$text\n\n");
    $text   = '';
    $previous_indent = 0;
    
    # builds blocks back together (we just force the
    # indentation between blocks)
    for ($k=1; $k<count($blocks); $k++)
    {
      $indent = $this->get_indentation($blocks[$k]);
      
      if ($indent > 0 and ($indent == $previous_indent or ($indent > $this->options['pre_width'] and $indent >= $previous_indent))):
        $text .= "\n".str_repeat(' ', $indent)."\n".$blocks[$k];
      else:
        $text .= "\n\n".$blocks[$k];
      endif;
      
      $previous_indent = $indent;
    }
    $this->text = $text;
  }
  
  # Returns the indentation (in spaces) for a given text block.
  private function get_indentation($line)
  {
    $line = preg_replace('/\t/', ' ', $line);
    return strlen($line) - strlen(ltrim($line, ' '));
  }
}

?>
