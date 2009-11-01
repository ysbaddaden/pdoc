<?php

function abc($a, $b=null, $c=array('a' => array('b' => 'c')))
{
  if ($a == 0) {
    echo $b;
  }
}

# this is a comment for def
function def()
{
  
}

function ghi($a=(true && false), $d='e', $e="fg")
{
  if ($a == 0) {
    echo $b;
  }
}

/* function jklmno() {} */

?>
