<?php

class A
{
  const name = 'A';
  const data = array('a', 'b', false);
}

# this is
# class B.
class B extends A implements ArrayAccess, Countable
{
  
}

?>
