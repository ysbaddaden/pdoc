<?php

function text_to_html($text)
{
  $parser = new TextParser($text);
  return $parser->transform();
}

class TextParser
{
  public $text;
  
  function __construct($text)
  {
    $this->text = $text;
    $this->linearize_text();
  }
  
  # Transforms the text to HTML.
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
      
      if ($indent >= 2)
      {
        $block = trim(preg_replace("/\n[ ]+/", "\n", $block));
        $html .= "<pre>".$block."</pre>";
      }
      else {
        $html .= "<p>".$block."</p>";
      }
    }
    return $html;
  }
  
  # Text pre-parser.
  # It linearizes the text by removing useless characters, building blocks, etc.
  private function linearize_text()
  {
    # cleanup
    $text = str_replace("\r", '', $this->text);
    $text = preg_replace('/\n+\s*\n+/m', "\n\n", $text);
    
    # separates blocks (separated by an empty line)
    $blocks = explode("\n\n", "\n\n$text\n\n");
    $text   = '';
    
    # builds blocks back together
    # we just force the indentation between blocks
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
