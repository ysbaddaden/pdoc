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
  
  function test_paragraphs()
  {
    $html = text_to_html("this is a\nparagraph\n\nthis is another\nparagraph");
    $this->assert_equal($html, "<p>this is a\nparagraph</p>\n<p>this is another\nparagraph</p>\n");
  }
  
  function test_preformated_code()
  {
    $html = text_to_html("para\n\n  <?php\n  \$a = 'b';\n  ?>");
    $this->assert_equal($html, "<p>para</p>\n<pre>&lt;?php\n\$a = 'b';\n?&gt;</pre>\n");
    /*
    $html = text_to_html("  <?php\n  \$a = 'b';\n  ?>");
    $this->assert_equal($html, "<pre>&lt;?php\n\$a = 'b';\n?&gt;</pre>\n");
    */
  }
  
  function test_definition_list()
  {
    $html = text_to_html("[test 1] *item 1*\n[+test 2+] item 2");
    $this->assert_equal($html, "<dl><dt>test 1</dt>\n<dd><strong>item 1</strong></dd>\n<dt><code>test 2</code></dt>\n<dd>item 2</dd>\n</dl>\n");

    $html = text_to_html("[+--main file+] file to use as index page\n[+--private+] document private methods");
    $this->assert_equal($html, "<dl><dt><code>--main file</code></dt>\n<dd>file to use as index page</dd>\n<dt><code>--private</code></dt>\n<dd>document private methods</dd>\n</dl>\n");
  }
  
  function test_ordered_list()
  {
    $html = text_to_html("1. item 1\n2. item 2\n3. item 3");
    $this->assert_equal($html, "<ol><li>item 1</li>\n<li>item 2</li>\n<li>item 3</li>\n</ol>\n");

    $html = text_to_html("1. *item 1*\n2. item +2+");
    $this->assert_equal($html, "<ol><li><strong>item 1</strong></li>\n<li>item <code>2</code></li>\n</ol>\n");
  }
  
  function test_unordered_list()
  {
    $html = text_to_html("- item 1\n- item 2\n- item 3");
    $this->assert_equal($html, "<ul><li>item 1</li>\n<li>item 2</li>\n<li>item 3</li>\n</ul>\n");
    
    $html = text_to_html("* *item* 1\n* item 2\n* item 3");
    $this->assert_equal($html, "<ul><li><strong>item</strong> 1</li>\n<li>item 2</li>\n<li>item 3</li>\n</ul>\n");
  }
  
  function test_text_to_html()
  {
    $html = text_to_html(file_get_contents(dirname(__FILE__).'/../fixtures/plain.txt'));
    $this->assert_equal(trim($html), trim(file_get_contents(dirname(__FILE__).'/../fixtures/plain.html')));
  }
  
}
new Test_SimpleMarkup();

?>
