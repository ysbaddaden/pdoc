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
  
  # Contains table name.
  protected $table_name;
  
  private static $private_data_2;
  static $static_var;
  
  function __construct() {
    $this->name = 'driver \'mysql\' { test }';
  }
  
   /**
   * Updates one attribute of record.
   * 
   *   $post = new Post(1);
   *   $post->name = 'my first post [update]';
   *   $post->update_attribute('name');
   *   
   *   $a = array(
   *     "A",
   *     "B",
   *   );
   *   $post->update_attribute('name', 'my first post [update 2]');
   */
  function update_attribute($attribute, $value=null)
  {
    $value   = (func_num_args() > 1) ? $value : $this->$attribute;
    $updates = array($attribute => $value);
    return $this->update_attributes(&$updates);
  }
  
  private function azerty()
  {
    
  }
  
  protected function merge_conditions($a, $b)
  {
    if (!empty($a) and empty($b)) {
      return $a;
    }
    if (empty($a) and !empty($b)) {
      return $b;
    }
    $a = $this->db->sanitize_sql_for_conditions($a);
    $b = $this->db->sanitize_sql_for_conditions($b);
    return "($a) AND ($b)";
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
