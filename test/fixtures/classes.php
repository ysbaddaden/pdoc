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
  function name()
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

?>
