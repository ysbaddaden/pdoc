<?php
include dirname(__FILE__).'/../test.php';
include dirname(__FILE__).'/../terminal.php';
include dirname(__FILE__).'/../../lib/simple_markup.php';

class Test_SimpleMarkup extends Unit_Test
{
  function test_span_to_html()
  {
    $html = span_to_html("+\$name = get_class('\$this')+ *bold*");
    $this->assert_equal($html, "<code>\$name = get_class('\$this')</code> <strong>bold</strong>");
    
    $html = span_to_html("See <tt>SimpleMarkup</tt> for help.");
    $this->assert_equal($html, 'See <a href="classes/SimpleMarkup.html">SimpleMarkup</a> for help.');
    
    $html = span_to_html("See http://example.com/pdoc for doc.");
    $this->assert_equal($html, 'See <a href="http://example.com/pdoc">http://example.com/pdoc</a> for doc.');
  }
  
  function test_text_to_html()
  {
    $html = text_to_html(file_get_contents(dirname(__FILE__).'/../fixtures/plain.txt'));
    $this->assert_equal($html, file_get_contents(dirname(__FILE__).'/../fixtures/plain.html'));
  }
}
new Test_SimpleMarkup();

?>
