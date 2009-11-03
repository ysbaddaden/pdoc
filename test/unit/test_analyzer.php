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
    $this->assert_equal(array_keys($classes), array('A', 'B', 'C'));
    $this->assert_equal($classes['A']['abstract'], true);
    $this->assert_equal($classes['B']['abstract'], false);
    $this->assert_equal($classes['A']['final'], false);
    $this->assert_equal($classes['B']['final'], true);
    $this->assert_equal($classes['B']['extends'], 'A');
    $this->assert_equal($classes['B']['implements'], array('ArrayAccess', 'Countable'));
    $this->assert_equal($classes['B']['comment'], "this is\nclass B.\n");
    
    # constants
    $this->assert_equal($classes['A']['constants'], array(
      'name' => array('value' => "'A'", 'comment' => "const A::A\n"),
      'data' => array('value' => "array('a', 'b', false)", 'comment' => ''),
    ));
    
    # attributes
#    $this->assert_equal(array_keys($classes['C']['attributes']), array('name', 'value', 'protect_me', 'paranoid'));
    
    # methods
    $this->assert_equal(array_keys($classes['B']['methods']), array('name', 'id'));
    $this->assert_equal($classes['B']['methods']['name']['comment'], "gets name\n");
    $this->assert_equal($classes['B']['methods']['id']['comment'], '');
    
    $this->assert_equal($classes['C']['methods']['a']['visibility'], 'public');
    $this->assert_equal($classes['C']['methods']['b']['visibility'], 'protected');
    $this->assert_equal($classes['C']['methods']['c']['visibility'], 'private');
    $this->assert_equal($classes['C']['methods']['d']['visibility'], 'public');
    $this->assert_equal($classes['C']['methods']['e']['visibility'], 'public');
    $this->assert_equal($classes['C']['methods']['f']['visibility'], 'private');
    
    $this->assert_true($classes['C']['methods']['c']['final']);
    $this->assert_false($classes['C']['methods']['d']['final']);
    $this->assert_true($classes['C']['methods']['e']['final']);
    
    $this->assert_false($classes['C']['methods']['a']['abstract']);
    $this->assert_true($classes['C']['methods']['b']['abstract']);
    
    $this->assert_true($classes['C']['methods']['d']['static']);
    $this->assert_false($classes['C']['methods']['e']['static']);
    $this->assert_true($classes['C']['methods']['f']['static']);
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
