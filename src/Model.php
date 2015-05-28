<?php

namespace Webbower\SimpleModel;

use \OutOfBoundsException;

/**
* Immutable PHP base model implementation inspired by Scala Case Classes
*
* This base model class should not expose ANY public properties. All property getting
* should be handled through the semantics defined in __get().
*
* There are 2 types of data that can be accessed via property syntax (`inst->property`):
* - Raw data: This is the raw data that is passed in to the constructor. These are accessed
*   by matching keys that were passed in to the constructor.
* - Derived data: This is dynamically generated data defined as instance methods. These are
*   special methods defined on the model subclass where the method name is prefixed with "get"
*   followed by a capitalized letter. See the example below.
*
* ``` php
* class Person extends SimpleModel {
*   public method getFullName()
*   {
*     return $this->firstName . ' ' . $this->lastName;
*   }
* }
*
* $me = new Person(['firstName' = 'Bob', 'lastName' => 'Smith']);
*
* // Raw data
* echo $me->firstName; // Bob
* echo $me->lastName;  // Smith
*
* // Derived data
* echo $me->fullName;  // Bob Smith
* ```
*
* @version 0.1.0
* @author Matt Bower <matt@webbower.com>
*
* @todo Add type checking
*/
class SimpleModel
{
  /**
   * @var string The "type" (class name) of this class instance
   */
  protected $class;

  /**
   * @var array The data stored in this model instance
   */
  protected $data;

  /**
   * Constructor
   *
   * @param array data The raw data held by this model instance
   * @return self An instance of the model
   */
  public function __construct(array $data)
  {
    $this->class = get_class($this);

    $this->data = $data;
  }

  /**
   * Provides interface to access data on model
   *
   * Provides access to the raw data stored in the internal `$data` map as well as the ability to retrieve
   * derived data defined as class methods as if they were also data properties (omitting `()`). Models
   * shouldn't have explicit public properties and data access should be controlled through here.
   *
   * @api
   *
   * @param string name The name of the data property to retrieve
   * @return mixed The data associated with the key
   * @throws OutOfBoundsException Thrown if the property key can't resolve to any data
   */
  final public function __get($name)
  {
    $getterMethodName = 'get' . ucfirst($name);

    if ($this->hasProperty($name)) {
      // Check for the raw data first
      return $this->data[$name];
    } elseif ($this->hasMethod($getterMethodName)) {
      // Check for a nullary method
      return $this->$getterMethodName();
    } else {
      // Can't find it. Fail.
      throw new OutOfBoundsException(sprintf("Error getting data %s: does not exist on %s", $name, $this->class));
    }
  }

  /**
   * Magic setter
   *
   * Model is immutable after instantiation and so setters are blocked. Use {@see copy()} instead
   *
   * @throws OutOfBoundsException
   */
  final public function __set($name, $value)
  {
    // if ($this->has($name)) {
    //   $this->data[$name] = $value;
    // } else {
    //   throw new Exception(sprintf("Error setting property %s: does not exist on %s", $name, __CLASS__));
    // }
    throw new OutOfBoundsException(sprintf("Cannot modify data properties on %s after instantiation", $this->class));
  }

  /**
   * Magic method for isset()
   *
   * Checks if the raw data key exists and if it's not null
   *
   * @api
   *
   * @param string name The name of the property to check
   * @return boolean True if the data exists and isn't null, false otherwise
   */
  final public function __isset($name)
  {
    return $this->hasProperty($name) && $this->$name !== null;
  }

  /**
   * Magic method for unset()
   *
   * Model is immutable after instantiation and so unsetting is blocked. Use {@see copy()} instead
   *
   * @throws OutOfBoundsException
   */
  final public function __unset($name)
  {
    throw new OutOfBoundsException(sprintf("Cannot unset data properties on %s after instantiation", $this->class));
  }

  /**
   * Get the underlying data array
   *
   * @api
   *
   * @return array An array representing the underlying raw data
   */
  public function toArray()
  {
    return $this->data;
  }

  /**
   * Test if the raw data key or derived data method exists
   *
   * This doesn't check if the data is null. Just if it exists.
   *
   * @api
   *
   * @param string name The data key/method name to check for
   * @return boolean True if the data/method exists, false otherwise
   */
  public function has($name)
  {
    return $this->hasProperty($name) || $this->hasMethod($name);
  }

  /**
   * Test if the raw data key exists
   *
   * This doesn't check if the data is null. Just if it exists.
   *
   * @api
   *
   * @param string name The data key to check for
   * @return boolean True if the data key exists, false otherwise
   */
  public function hasProperty($name)
  {
    return array_key_exists($name, $this->data);
  }

  /**
   * Test if the derived data method exists
   *
   * This doesn't check if the returned value is null. Just if the method exists.
   *
   * @api
   *
   * @param string name The method name to check for
   * @return boolean True if the method name exists, false otherwise
   */
  public function hasMethod($name)
  {
    return method_exists($this, $name);
  }

  /**
   * Creates a new version of this model with changed data
   *
   * You only need to pass in the keys you want to change
   *
   * @api
   *
   * @param array data An assoc array of the data to change
   * @return self Returns a new instance of this class with updated data
   */
  public function copy(array $data)
  {
    $newClass = $this->class;

    return new $newClass(array_merge($this->data, $data));
  }
}
