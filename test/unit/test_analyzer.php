<?php
include dirname(__FILE__).'/../test.php';
include dirname(__FILE__).'/../terminal.php';
include dirname(__FILE__).'/../../lib/analyzer.php';

class Test_Pdoc_Analyzer extends Unit_Test
{
  function test_functions_and_arguments()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/code.php');
    
    $functions = $analyzer->functions();
    
    # args
    $this->assert_equal(array_keys($functions), array('abc', 'def', 'ghi'));
    $this->assert_equal($functions['abc']['arguments'], "\$a, \$b=null, \$c=array('a' => array('b' => 'c'))");
    $this->assert_equal($functions['ghi']['arguments'], "\$a=(true && false), \$d='e', \$e=\"fg\"");
    
    # comments
    $this->assert_equal($functions['def'], array('arguments' => '', 'comment' => "this is a comment\nfor def\n"));
    $this->assert_equal($functions['abc']['comment'], "This is a doc\nblock test.");
    $this->assert_equal($functions['ghi']['comment'], "this is\na test");
  }
}
new Test_Pdoc_Analyzer();

?>
