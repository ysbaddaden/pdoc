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

class SimpleMarkup
{
  public $text;
  public $options = array(
    'pre_width'      => 2,
    'headings_start' => 2,
  );
  
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
        $block = htmlspecialchars(trim(preg_replace("/\n[ ]{".$indent."}/", "\n", $block)));
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
