<?php

use \Webbower\SimpleModel\SimpleModel;

class SimpleModelTest extends PHPUnit_Framework_TestCase
{
  protected $model;

  protected function setUp() {
    $this->model = new \Webbower\SimpleModel\Test\Person([
      'firstName' => 'Bob',
      'lastName'  => 'Smith',
      'age'       => 32,
      'married'   => false,
      'kids'      => [],
      'house'     => null,
    ]);
  }

  protected function tearDown() {
    unset($this->model);
  }

  public function testSimpleModelCanBeCreated()
  {
    $this->assertInstanceOf('\Webbower\SimpleModel\SimpleModel', $this->model);
    $this->assertInstanceOf('\Webbower\SimpleModel\Test\Person', $this->model);
  }
  
  public function testGetterForRawData()
  {
    $this->assertEquals('Bob',   $this->model->firstName);
    $this->assertEquals('Smith', $this->model->lastName);
    $this->assertEquals(32,      $this->model->age);
    $this->assertEquals(false,   $this->model->married);
    $this->assertEquals([],      $this->model->kids);
    $this->assertEquals(null,    $this->model->house);
  }

  public function testGetterForDerivedData()
  {
    $this->assertEquals('Bob Smith', $this->model->fullName);
  }

  /**
   * @expectedException         OutOfBoundsException
   * @expectedExceptionMessage  Error getting data foo: does not exist on Webbower\SimpleModel\Test\Person
   */
  public function testGetterThrowsExceptionOnNonexistantData()
  {
    $this->model->foo;
  }

  /**
   * @expectedException         OutOfBoundsException
   * @expectedExceptionMessage  Error getting data someMethod: does not exist on Webbower\SimpleModel\Test\Person
   */
  public function testGetterThrowsExceptionOnRegularMethodAsProperty()
  {
    $this->model->someMethod;
  }

  /**
   * @expectedException         OutOfBoundsException
   * @expectedExceptionMessage  Cannot modify data properties on Webbower\SimpleModel\Test\Person after instantiation
   */
  public function testSetterThrowsException()
  {
    $this->model->age = 33;
  }

  public function testIsset()
  {
    $this->assertTrue( isset($this->model->firstName));
    $this->assertTrue( isset($this->model->lastName));
    $this->assertTrue( isset($this->model->age));
    $this->assertTrue( isset($this->model->married));
    $this->assertTrue( isset($this->model->kids));
    $this->assertFalse(isset($this->model->house));
    $this->assertFalse(isset($this->model->foo));
  }

  /**
   * @expectedException         OutOfBoundsException
   * @expectedExceptionMessage  Cannot unset data properties on Webbower\SimpleModel\Test\Person after instantiation
   */
  public function testUnsetThrowsException()
  {
    unset($this->model->age);
  }

  public function testHasProperty()
  {
    $this->assertTrue( $this->model->hasProperty('firstName'));
    $this->assertTrue( $this->model->hasProperty('lastName'));
    $this->assertTrue( $this->model->hasProperty('age'));
    $this->assertTrue( $this->model->hasProperty('married'));
    $this->assertTrue( $this->model->hasProperty('kids'));
    $this->assertTrue( $this->model->hasProperty('house'));
    $this->assertFalse($this->model->hasProperty('foo'));
  }

  public function testToArray()
  {
    $data = [
      'firstName' => 'Bob',
      'lastName'  => 'Smith',
      'age'       => 32,
      'married'   => false,
      'kids'      => [],
      'house'     => null,
    ];
    
    $this->assertEquals($data, $this->model->toArray());
  }

  public function testCopy()
  {
    $copy = $this->model->copy([
      'age'     => 35,
      'married' => true,
      'kids'    => ['Sally'],
    ]);

    // Assert the original and copy aren't the same
    $this->assertNotEquals($this->model, $copy);
    
    // Assert the copy is of the same classes
    $this->assertInstanceOf('\Webbower\SimpleModel\SimpleModel', $copy);
    $this->assertInstanceOf('\Webbower\SimpleModel\Test\Person', $copy);

    // Assert changed values on copy
    $this->assertEquals('Bob',     $copy->firstName);
    $this->assertEquals('Smith',   $copy->lastName);
    $this->assertEquals(35,        $copy->age);
    $this->assertEquals(true,      $copy->married);
    $this->assertEquals(['Sally'], $copy->kids);
    $this->assertEquals(null,      $copy->house);

    // Assert retained values on original
    $this->assertEquals('Bob',   $this->model->firstName);
    $this->assertEquals('Smith', $this->model->lastName);
    $this->assertEquals(32,      $this->model->age);
    $this->assertEquals(false,   $this->model->married);
    $this->assertEquals([],      $this->model->kids);
    $this->assertEquals(null,    $this->model->house);
  }
}
