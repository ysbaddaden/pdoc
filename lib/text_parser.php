<?php

function text_to_html($text, $options=null)
{
  $parser = new TextParser($text, $options);
  return $parser->transform();
}

function span_to_html($block)
{
  $parser = new TextParser('');
  return $parser->parse_span($block);
}

class TextParser
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
    $this->text = $text;
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
        $html .= "<h{$hx}>{$m[2]}</h{$hx}>\n";
      }
      elseif(preg_match('/^[\-\*] /s', $block, $m))
      {
        # list (unordered)
        $block = $this->parse_list($block);
        $html .= "<ul>".$this->parse_span($block)."</ul>\n";
      }
      else
      {
        # paragraph
        $html .= "<p>".$this->parse_span($block)."</p>\n";
      }
    }
    return $html;
  }
  
  function parse_span($block)
  {
    $block = preg_replace('/`(.+?)`/', '<code>\1</code>', $block);
    $block = preg_replace_callback('/\+(.+?)\+/', array($this, '__replace_links'), $block);
    $block = preg_replace_callback('/(?:http|https|ftp|sftp|ssh):\/\/[^ ]+/', array($this, '__cb_auto_link'), $block);
    return $block;
  }
  
  static private function __cb_auto_link($match)
  {
    return '<a href="'.htmlspecialchars($match[0]).'">'.$match[0].'</a>';
  }
  
  # @private
  static function __replace_links($match)
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
  
  # Parses a list.
  # 
  # TODO: Parse sublists.
  protected function parse_list($block)
  {
    $block = "\n$block\n";
    preg_match_all('/\n[\-\*] (.+)/', $block, $matches, PREG_SET_ORDER);

    $html = '';
    foreach($matches as $match) {
      $html .= "<li>{$match[1]}</li>\n";
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
