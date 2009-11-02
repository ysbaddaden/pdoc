<?php
include dirname(__FILE__).'/../test.php';
include dirname(__FILE__).'/../terminal.php';
include dirname(__FILE__).'/../../lib/analyzer.php';

class Test_Pdoc_Analyzer extends Unit_Test
{
  function test_functions_and_arguments()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/functions.php');
    
    $functions = $analyzer->functions();
    
    # args
    $this->assert_equal(array_keys($functions), array('abc', 'def', 'ghi', 'jkl'));
    $this->assert_equal($functions['abc']['arguments'], "\$a, \$b=null, \$c=array('a' => array('b' => 'c'))");
    $this->assert_equal($functions['def']['arguments'], '');
    $this->assert_equal($functions['ghi']['arguments'], "array \$a=(true && false), \$d='e', \$e=\"fg\"");
    
    # comments
    $this->assert_equal($functions['def']['comment'], "this is a comment\nfor def\n");
    $this->assert_equal($functions['abc']['comment'], "This is a doc\nblock test.");
    $this->assert_equal($functions['ghi']['comment'], "this is\na test");
    $this->assert_equal($functions['jkl']['comment'], '');
  }
  
  function test_classes()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/classes.php');
    
    $classes = $analyzer->classes();
    
    # definition
    $this->assert_equal(array_keys($classes), array('A', 'B'));
    $this->assert_equal($classes['B']['extends'], 'A');
    $this->assert_equal($classes['B']['implements'], array('ArrayAccess', 'Countable'));
    
    # constants
    $this->assert_equal($classes['A']['constants'], array(
      'name' => array('value' => "'A'", 'comment' => ''),
      'data' => array('value' => "array('a', 'b', false)", 'comment' => ''),
    ));
    
    # methods
    $this->assert_equal(array_keys($classes['B']['methods']), array('name', 'id'));
    
    # comments
    $this->assert_equal($classes['B']['comment'], "this is\nclass B.\n");
    $this->assert_equal($classes['B']['methods']['name']['comment'], "gets name\n");
    $this->assert_equal($classes['B']['methods']['id']['comment'], '');
  }
  
  function test_interfaces()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/interfaces.php');
    
    $interfaces = $analyzer->interfaces();
    
    # definition
    $this->assert_equal(array_keys($interfaces), array('A', 'B'));
    $this->assert_equal($interfaces['B']['extends'], array('A', 'ArrayAccess'));
    $this->assert_equal($interfaces['B']['comment'], "this is\ninterface B\n");
   
    # constants
    $this->assert_equal($interfaces['A']['constants'], array(
      'aze' => array('value' => 'false', 'comment' => 'aze is false'),
      'rty' => array('value' => "array('name' => 'a;b')", 'comment' => '')
    ));
    
    # method declarations
    $this->assert_equal(array_keys($interfaces['B']['methods']), array('test', 'another_test'));
    $this->assert_equal($interfaces['B']['methods']['another_test']['arguments'], '$a=null');
    $this->assert_equal($interfaces['B']['methods']['another_test']['comment'], "this is another\n test\n");
  }
}
new Test_Pdoc_Analyzer();

?>
