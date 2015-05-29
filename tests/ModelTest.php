<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
  protected $model;

  protected function setUp() {
    $this->model = new \Webbower\SimpleData\Test\Person([
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

  public function testModelCanBeCreated()
  {
    $this->assertInstanceOf('\Webbower\SimpleData\Model', $this->model);
    $this->assertInstanceOf('\Webbower\SimpleData\Test\Person', $this->model);
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
   * @expectedExceptionMessage  Error getting data foo: does not exist on Webbower\SimpleData\Test\Person
   */
  public function testGetterThrowsExceptionOnNonexistantData()
  {
    $this->model->foo;
  }

  /**
   * @expectedException         OutOfBoundsException
   * @expectedExceptionMessage  Error getting data someMethod: does not exist on Webbower\SimpleData\Test\Person
   */
  public function testGetterThrowsExceptionOnRegularMethodAsProperty()
  {
    $this->model->someMethod;
  }

  /**
   * @expectedException         OutOfBoundsException
   * @expectedExceptionMessage  Cannot modify data properties on Webbower\SimpleData\Test\Person after instantiation
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
   * @expectedExceptionMessage  Cannot unset data properties on Webbower\SimpleData\Test\Person after instantiation
   */
  public function testUnsetThrowsException()
  {
    unset($this->model->age);
  }

  public function testHasProperty()
  {
    $this->assertTrue( $this->model->hasField('firstName'));
    $this->assertTrue( $this->model->hasField('lastName'));
    $this->assertTrue( $this->model->hasField('age'));
    $this->assertTrue( $this->model->hasField('married'));
    $this->assertTrue( $this->model->hasField('kids'));
    $this->assertTrue( $this->model->hasField('house'));
    $this->assertFalse($this->model->hasField('foo'));
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
    $this->assertInstanceOf('\Webbower\SimpleData\Model', $copy);
    $this->assertInstanceOf('\Webbower\SimpleData\Test\Person', $copy);

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
