# Model class API

### `Model::$fields`

A static, protected property that you define on your Model subclasses to define the model schema.

```php
protected static $fields = [
  'firstName' => 'String',
  'lastName'  => 'String',
  'age'       => 'Integer',
  'spouse'    => 'Person',
];
```

For primitive PHP data types, the value aligns with the output of PHP's native `gettype()` function except that it is capitalized:

Possible type values are:

* Boolean - A boolean value
* Integer - A whole number (no decimal)
* Double - A floating point number (with decimal)
* String - A string
* Array - An array (indexed or associative)

Additionally, any class name can be used as a type.

Any field can also be `null`

### `Model::__construct(array $data)`

The constructor. Takes an associative array of keys and data values. The data passed in will be type-checked against the `$fields` property. Any type mismatches or unexpected keys will cause an `OutOfBoundsException` to be thrown. You do not have to set every schema key on instantiation. Any keys omitted will be set to `null`.

### `isset($var)`

Using the `__isset()` magic method, this will return `true` if the variable passed in is part of the defined schema and is not `null` or `false` otherwise. Attempting to check if a derived data property exists will always return `false`.

Using the earlier `Person` class:

```php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  // 'spouse' is null because it was omitted
]);

var_export(isset($me->firstName)); // true
var_export(isset($me->spouse)); // false (because it is null)
var_export(isset($me->foo)); // false (because it's not part of the schema)
```

### `Model::toArray()`

Returns the underlying raw data structure as an array. If you omitted any fields on instantiation, they will be provided as `null` values.

Using the earlier `Person` class:

```php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  // 'spouse' is null because it was omitted
]);

print_r($me->toArray()); // [ 'firstName' => 'Bob', 'lastName' => 'Smith', 'age' => 45, 'spouse' => null ]
```

### `Model::hasField(string $name)`

Given `$name`, returns `true` if the raw data field exists (regardless of value) and `false` otherwise

```php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  // 'spouse' is null because it was omitted
]);

var_export($me->hasField('firstName')); // true
var_export($me->hasField('lastName')); // true
var_export($me->hasField('foo')); // false
```

### `Model::hasMethod(string $name)`

Given `$name`, returns `true` if the method exists (not necessarily derived data methods) and `false` otherwise

```php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  // 'spouse' is null because it was omitted
]);

var_export($me->hasMethod('getFullName')); // true
var_export($me->hasMethod('someMethod')); // true
var_export($me->hasMethod('anotherMethod')); // false
```

### `Model::has(string $name)`

Given `$name`, returns `true` if the raw data field of method exists and `false` otherwise

```php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  // 'spouse' is null because it was omitted
]);

var_export($me->has('firstName')); // true
var_export($me->has('getFullName')); // true
var_export($me->has('someMethod')); // true
var_export($me->has('anotherMethod')); // false
```

### `Model::copy(array $data)`

Takes an associative array of data and returns a copy of the original instance with the passed-in data keys updated. Not all keys need to be present and only the keys passed in will be updated. Type checking semantics are identical to the constructor. This is the way to update data on your model, and in an atomic manner.

```php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  // 'spouse' is null because it was omitted
]);

// Happy birthday!!
$olderMe = $me->copy([
  'age'     => 46,
]);

echo $me->age; // 45
echo $olderMe->age; // 46
```
