<?php
include dirname(__FILE__).'/../test.php';
include dirname(__FILE__).'/../terminal.php';
include dirname(__FILE__).'/../../lib/pdoc/analyzer.php';

class Test_Pdoc_Klass extends Unit_Test
{
  function test_setup()
  {
    $analyzer = new Pdoc_Analyzer();
    $analyzer->add(dirname(__FILE__).'/../fixtures/classes.php');
    $this->classes = $analyzer->classes();
  }
  
  function test_attributes()
  {
    $attributes = $this->classes['C']->attributes(array('static' => true, 'visibility' => 'public'));
    $this->assert_equal(array_keys($attributes), array('$name'));
    
    $attributes = $this->classes['C']->attributes(array('static' => false, 'visibility' => 'public'));
    $this->assert_equal(array_keys($attributes), array());
    
    $attributes = $this->classes['C']->attributes(array('visibility' => 'private'));
    $this->assert_equal(array_keys($attributes), array('$paranoid', '$value'));
  }
  
  function test_methods()
  {
    $methods = $this->classes['C']->methods(array('static' => true, 'visibility' => 'public'));
    $this->assert_equal(array_keys($methods), array('d'));
    
    $methods = $this->classes['C']->methods(array('static' => false, 'visibility' => 'public'));
    $this->assert_equal(array_keys($methods), array('a', 'e'));
    
    $methods = $this->classes['C']->methods(array('visibility' => 'private'));
    $this->assert_equal(array_keys($methods), array('c', 'f'));
  }
  
  function test_has_attributes()
  {
    $this->assert_false($this->classes['C']->has_attributes(array('static' => true, 'visibility' => 'protected')));
    $this->assert_true($this->classes['C']->has_attributes(array('static' => false)));
  }
  
  function test_has_constants()
  {
    $this->assert_false($this->classes['C']->has_constants());
    $this->assert_true($this->classes['A']->has_constants());
  }
  
  function test_has_methods()
  {
    $this->assert_false($this->classes['C']->has_methods(array('static' => true, 'visibility' => 'protected')));
    $this->assert_true($this->classes['C']->has_methods(array('static' => false)));
  }
}
new Test_Pdoc_Klass();

?>
