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

# Just another test.
#
# Really, it's just another test!
class NS_AnotherTest extends NS_Fake implements Toto, blabla
{
  
}

  /**
   * Adds timestamp columns to a table's definition.
   * 
   * $type can be:
   * 
   * - date: will create created_on & updated_on.
   * - time: will create created_at & updated_at.
   * - datetime: will create created_at & updated_at.
   * 
   * Examples:
   *
   *   $test = new NS_YetAnotherTest();
   *   
   *   $test->azerty = 'blabla';
   */
class NS_YetAnotherTest implements Toto, blabla extends NS_Fake
{
  
}

?>
