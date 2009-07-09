<?php

function text_to_html($text)
{
  $parser = new TextParser($text);
  return $parser->transform();
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
        $html .= "<ul>$block</ul>\n";
      }
      else
      {
        # paragraph
        $html .= "<p>$block</p>\n";
      }
    }
    return $html;
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
  
  # Text pre-parser.
  # 
  # It linearizes plain text by removing useless characters,
  # building blocks, etc.
  private function linearize_text()
  {
    # cleanup
    $text = str_replace("\r", '', $this->text);
    $text = preg_replace('/\n+\s*\n+/m', "\n\n", $text);
    
    # separates blocks (separator is an empty line)
    $blocks = explode("\n\n", "\n\n$text\n\n");
    $text   = '';
    
    # builds blocks back together (we just force the
    # indentation between blocks)
    for ($k=1; $k<count($blocks); $k++)
    {
      $indent = $this->get_indentation($blocks[$k]);
      $previous_indent = $this->get_indentation($blocks[$k-1]);
      
      if ($indent > 0 and $indent == $previous_indent):
        $text .= "\n".str_repeat(' ', $indent)."\n".$blocks[$k];
      else:
        $text .= "\n\n".$blocks[$k];
      endif;
    }
    $this->text = $text;
  }
  
  # Returns the indentation (in spaces) for a given text.
  private function get_indentation($line)
  {
    $line = preg_replace('/\t/', ' ', $line);
    return strlen($line) - strlen(ltrim($line, ' '));
  }
}

?>
