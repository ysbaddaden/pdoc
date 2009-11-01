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
    $this->assert_equal(array_keys($functions), array('abc', 'def', 'ghi'));
    $this->assert_equal($functions['def'], array('arguments' => ''/*, 'comment' => 'this is a comment for def'*/));
    $this->assert_equal($functions['abc'], array('arguments' => "\$a, \$b=null, \$c=array('a' => array('b' => 'c'))"));
    $this->assert_equal($functions['ghi'], array('arguments' => "\$a=(true && false), \$d='e', \$e=\"fg\""));
  }
}
new Test_Pdoc_Analyzer();

?>
