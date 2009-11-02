<?php

interface A
{
  const aze = false;
  const rty = array('name' => 'a;b');
}

// this is
// interface B
interface B extends A, ArrayAccess
{
  
}

?>
