<?php

class Pdoc_Klass extends ArrayObject
{
  # Returns a list of class constants.
  function & constants()
  {
    return $this['constants'];
  }
  
  # Returns a filtered list of class attributes.
  # 
  # Example:
  # 
  #   $attributes = $class->attributes(array('static' => false));
  # 
  # Filters:
  #
  # - visibility: either public, protected or private
  # - static (bool)
  # 
  function & attributes($filters=array())
  {
    return $this->filter($this['attributes'], $filters);
  }
  
  # Returns a filtered list of class methods.
  # 
  # Filters:
  #
  # - visibility: either public, protected or private
  # - static (bool)
  # - abstract (bool)
  # - final (bool)
  # 
  function & methods($filters=array())
  {
    return $this->filter($this['methods'], $filters);
  }
  
  # Checks if the class has any method maching filters.
  # See +methods+ for the list of filters.
  function has_methods($filters=array())
  {
    return ($this->count_filter($this['methods'], $filters) > 0);
  }
  
  # Checks if the class has any attribute maching filters.
  # See +attributes+ for the list of filters.
  function has_attributes($filters=array())
  {
    return ($this->count_filter($this['attributes'], $filters) > 0);
  }
  
  private function & filter($ary, $filters)
  {
    foreach($filters as $k => $v)
    {
      foreach(array_keys($ary) as $name)
      {
        if ($ary[$name][$k] !== $v) {
          unset($ary[$name]);
        }
      }
    }
    return $ary;
  }
  
  private function count_filter($ary, $filters)
  {
    $count = 0;
    foreach(array_keys($ary) as $name)
    {
      $match = true;
      
      foreach($filters as $k => $v)
      {
        if ($ary[$name][$k] !== $v)
        {
          $match = false;
          break;
        }
      }
      
      $match && $count++;
    }
    return $count;
  }
}

?>
