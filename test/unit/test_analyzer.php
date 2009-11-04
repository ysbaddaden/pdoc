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
    
    $this->assert_true($analyzer->function_exists('abc'));
    $this->assert_false($analyzer->function_exists('unknown_function'));
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
    $this->assert_equal(array_keys($classes['C']['attributes']), array('$name', '$value', '$protect_me', '$paranoid'));
    $this->assert_equal($classes['C']['attributes']['$name'],
      array('comment' => '', 'value' => null, 'visibility' => 'public', 'static' => true));
    $this->assert_equal($classes['C']['attributes']['$value'],
      array('comment' => '', 'value' => 'null', 'visibility' => 'private', 'static' => true));
    $this->assert_equal($classes['C']['attributes']['$protect_me'],
      array('comment' => '', 'value' => 'false', 'visibility' => 'protected', 'static' => false));
    $this->assert_equal($classes['C']['attributes']['$paranoid'],
      array('comment' => "shall we be paranoid?\n", 'value' => "array('a' => array('b'))", 'visibility' => 'private', 'static' => false));
    
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
    
    $this->assert_true($analyzer->class_exists('C'));
    $this->assert_false($analyzer->class_exists('UnknownClass'));
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
    
    $this->assert_true($analyzer->interface_exists('A'));
    $this->assert_false($analyzer->interface_exists('UnknownInterface'));
  }
  
  function test_pseudo_namespaces()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/pseudo_namespaces.php');
    $namespaces = $analyzer->namespaces();
    
    $this->assert_equal(array_keys($namespaces), array('Ns', 'Ns\SubNs', 'Ifaces'));
#    $this->assert_equal(array_keys($namespaces['Ns']['functions']), array('Ns_find'));
    $this->assert_equal(array_keys($namespaces['Ns']['classes']),   array('Ns_Klass'));
    $this->assert_equal(array_keys($namespaces['Ns\SubNs']['classes']),  array('Ns_SubNs_Klass'));
    $this->assert_equal(array_keys($namespaces['Ifaces']['interfaces']), array('Ifaces_Object'));
  }
  
  function test_namespaces()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/namespace_ns.php');
    $analyzer->add(dirname(__FILE__).'/../fixtures/namespace_ns_subns.php');
    $namespaces = $analyzer->namespaces();
    
    $this->assert_equal(array_keys($namespaces), array('Ns', 'Ns\SubNs'));
    $this->assert_equal(array_keys($namespaces['Ns']['classes']), array('Ns\SubKlass'));
    $this->assert_equal(array_keys($namespaces['Ns']['functions']), array('Ns\my_func'));
    $this->assert_equal(array_keys($namespaces['Ns']['interfaces']), array('Ns\Iface'));
  }
  
  function test_methods()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/pseudo_namespaces.php');
    $methods = $analyzer->methods();
    
    $this->assert_equal(array_keys($methods), array('another_test (Ns_Klass)', 'Ns_find', 'test (Ns_Klass)', 'test (Ns_SubNs_Klass)'));
    $this->assert_equal($methods['test (Ns_Klass)'], array('visibility' => 'public', 'path' => 'Ns_Klass::test'));
    $this->assert_equal($methods['another_test (Ns_Klass)'], array('visibility' => 'private', 'path' => 'Ns_Klass::another_test'));
    $this->assert_equal($methods['Ns_find'], array('visibility' => 'public', 'path' => 'Ns_find'));
  }
}
new Test_Pdoc_Analyzer();

?>
