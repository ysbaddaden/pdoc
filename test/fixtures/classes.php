<?php

abstract class A
{
  # const A::A
  const name = 'A';
  const data = array('a', 'b', false);
}

# this is
# class B.
final class B extends A implements ArrayAccess, Countable
{
  public $name;
  
  # gets name
  static protected function name()
  {
    # irrelevant comment
    return $this->name;
  }
  
  function id($id=null)
  {
    if ($id !== null) {
      $this->id = $id;
    }
    return $this->id;
  }
}

class C
{
  static $name;
  private static $value;
  protected $protect_me;
  private $paranoid;
  
  public function a()
  {
    
  }
  
  abstract protected function b()
  {
    
  }
  
  final private function c()
  {
    
  }
  
  static function d()
  {
    
  }
  
  final function e()
  {
    
  }
  
  private static function f()
  {
    
  }
}

?>
