<?php

namespace Webbower\SimpleData\Test;

use \Webbower\SimpleData\Model;

class Person extends Model
{
  // protected static $fields = [
  //   'firstName' => 'String',
  //   'lastName'  => 'String',
  //   'age'       => 'Integer',
  //   'salary'    => 'Double',
  //   'married'   => 'Boolean',
  //   'kids'      => 'Array',
  // ];

  /**
   * Derived data method
   */
  public function getFullName()
  {
    return $this->firstName . ' ' . $this->lastName;
  }

  /**
   * Regular method
   */
  public function someMethod()
  {
    return 'foo';
  }
}