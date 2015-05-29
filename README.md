# SimpleData for PHP

An immutable base model class for PHP inspired by Case Classes in Scala

**Note:** This is currently in alpha and not all features listed are fully implemented yet. My goal is to make the minor releases add related features. I'm also working out a few ideas that haven't quite gelled yet so the API will obviously be changing.

## WAT?

Currently model/ORM/etc libraries are enormous and somewhat intertwined. My goal was to make a lightweight, focused, base class for creating models for an application and some support tools for them. The (still in-progress for 1.0) goal is to make something that is simple to configure, treats data as data (instead of explicit getter/setter methods. I mean, c'mon. Is this really Java?), employs faux type enforcement, and fails loudly if you do something you didn't define. I also wanted to decouple the in-memory data representation from the persistence layer and make the model focus primarily on being data and not being explicitly coupled to other classes. I decided to throw immutability in the mix due to my recent crush on Functional Programming.

The code conforms to PSR-0 and PSR-4 autoloading as well as PSR-1 and PSR-2 coding conventions

### Simplicity

Defining a new "data type" (subclass) is simple:

```php
<?php

use \Webbower\SimpleData\Model;

class Person extends Model
{
  // To be implemented
  protected static $fields = [
    'firstName' => 'String',
    'lastName'  => 'String',
    'age'       => 'Integer',
    'spouse'    => 'Person',
  ];

  public function getFullName()
  {
    return $this->firstName . ' ' . $this->lastName;
  }

  public function someMethod()
  {
    return 'foo';
  }
}
```

So what's going on here?

1. Subclass `Model` to make a new `Person` "type"
2. Define the raw data "schema" in `$fields` where each key is the data name and each value is the type
3. Define a derived data method that can be called with the property access syntax

### Data is Data

Data is data is data. Why do we need to have explicit getter and setter methods for each piece of data. Model smooths this over. It also takes from Scala (from my personal experience) the [Uniform Access Principle](http://en.wikipedia.org/wiki/Uniform_access_principle), most notably where nullary methods can drop the trailing `()`. So, in Model terms, "data" is anything that can be accessed with instance property syntax (`$inst->property`).

Model's data representation provides for 2 type of data:

* Raw data: This is the raw data that is passed in to the constructor. These are accessed by matching keys that were passed in to the constructor and defined on the static `$fields` property.
* Derived data: This is dynamically generated data defined as instance methods. These are special getter methods defined on the model subclass where the method name is prefixed with "get" followed by a capitalized letter. For example, the `getFullName()` method can be accessed as `$inst->fullName`. If a method doesn't follow this naming convention, it will not be accessible via the property syntax and will throw an exception.

Using the above `Person` class:

```php
<?php
$me = new Person([
  'firstName' => 'Bob',
  'lastName' => 'Smith',
  'age' => 45,
  'spouse' => new Person([
    'firstName' => 'Sally',
    'lastName' => 'Smith',
    'age' => 46,
    'spouse' => $me, // Not sure if this works
  ])
]);

echo $me->firstName; // Bob
echo $me->lastName;  // Smith
echo $me->age;  // 45
echo $me->fullName;  // Bob Smith
echo $me->spouse->fullName; // Sally Smith
echo $me->foo; // throws exception
echo $me->someMethod; // throws exception
```

### Immutability

Taking a cue from Functional Programming, Models are immutable after instantiation. This helps avoid problems around state. So how do you update the data in your model if, say, a form or API request is submitted to modify the data record?

```php
<?php
// Using made up persistence code

// Fetch the current record from the data store
$me = PersonDatabase::getById($id);

// Get the array of changed keys, pass it to copy() to make a new instance, persist it, and cache the updated record in a variable to send back.
if ($newMe = PersonDatabase::save($me->copy($me->diff($requestData)))) {
  echo "Saved!";
  return new JsonResponse($me->toJson());
} else {
  echo "There was a problem";
}

```

### Type Enforcement

**Note:** This has not be implemented yet.

The static `$fields` property of a class defines the model schema and allowed types. Any field can be `null` in addition to its defined type. The types are checked on instantiation, either from calling the constructor, or `copy()` (which calls the constructor).

Referring to the earlier example:

```php
protected static $fields = [
  'firstName' => 'String',
  'lastName'  => 'String',
  'age'       => 'Integer',
  'spouse'    => 'Person',
];
```

4 fields are defined:

* `firstName` which must be a string
* `lastName` which must be a string
* `age` which must be an integer (no decimals)
* `spouse` which must be an instance of the `Person` class

Attempting to set any of these fields to anything except `null` and what they are typed as will throw an exception.

### Fails Loudly

I believe that code should fail loudly when it gets unexpected data or is used in unsupported ways. Model throws exceptions for the following reasons:

* Trying to set a field to something other than `null` or its defined typed
* Attempting to get a property that isn't defined (as raw or derived data)
* Attempting to set a property (e.g. `$inst->firstName = 'John'`) or unset a property (e.g. `unset($inst->firstName)`)

### Decoupled

Model is meant to represent data, not also provide a persistence API. This way, you can choose your data wrapper and your persistence system a la carte. However (yet to be implemented), Model instances can have persistence schema generation and input/output methods attached to it via traits or interfaces. After all, a model defines an API  for an in-memory data structure. Persistence libraries would then call the appropriate methods on the Model class/instance to assist in generating the queries to read and persist the data.

## API

The API is fairly simple:

* [Model](docs/model.md)

## Todo

* Implement type enforcement
* Confirm Model subclasses can be subclassed and work as expected where each defined `$fields` schema combines with parent types.
* Extend type enforcement for sub-types: 'kids' => 'Array[Person]', 'kids' => 'Collection[Person]', 'foo' => 'Array[String,Integer]'. Make recursive?
* Add diffing method that takes an array of data and returns an array with only the keys that are allowed and different than those currently in-place
* Constructor and/or `copy()` should throw if attempting to set keys not defined in schema
* Consider making `hasField()` also check for derived data methods using the property accessor name (e.g. get for `getFullName()` method with `$inst->hasField('fullName')`)
* Implement Travis integration
* Contribute to Packagist
