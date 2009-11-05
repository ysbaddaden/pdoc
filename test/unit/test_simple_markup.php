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
    $this->assert_equal(trim($html), trim(file_get_contents(dirname(__FILE__).'/../fixtures/plain.html')));
  }
  
  function test_definition_list()
  {
    $html = text_to_html("[test 1] item 1\n[test 2] item 2");
    $this->assert_equal($html, "<dl><dt>test 1</dt>\n<dd>item 1</dd>\n<dt>test 2</dt>\n<dd>item 2</dd>\n</dl>\n");
  }
  
  function test_ordered_list()
  {
    $html = text_to_html("1. item 1\n2. item 2\n3. item 3");
    $this->assert_equal($html, "<ol><li>item 1</li>\n<li>item 2</li>\n<li>item 3</li>\n</ol>\n");
  }
}
new Test_SimpleMarkup();

?>
