<?php

abstract class NS_Fake
{
  
}

class NS_TestFake extends NS_Fake
{
  function run_tests()
  {
    if (true) {
      return;
    }
  }
}

interface database
{
  
}

interface blabla
{
  
}

class mysql implements database
{
  public $name;
  
  function __construct() {
    $this->name = 'driver \'mysql\' { test }';
  }
}

class NS_AnotherTest extends NS_Fake implements Toto, blabla
{
  
}

class NS_YetAnotherTest implements Toto, blabla extends NS_Fake
{
  
}

?>
