<?php

/**
 * This is a doc
 * block test.
 */
function abc($a, $b=null, $c=array('a' => array('b' => 'c')))
{
  if ($a == 0) {
    echo $b;
  }
}

  # this comment
  # is discarded
  
  # this is a comment
  # for def
  function def()
  {
    
  }

/*
this is
a test
*/
function ghi(array $a=(true && false), $d='e', $e="fg")
{
  if ($a == 0) {
    echo $b;
  }
  # irrelevant comment
}

function jkl()
{
  
}

/* function jklmno() {} */

?>
