<?php

interface A
{
  /* aze is false */
  const aze = false;
  const rty = array('name' => 'a;b');
}

// this is
// interface B
interface B extends A, ArrayAccess
{
  function test();
  
  # this is another
  #  test
  function another_test($a=null);
}

?>
