## Simple Validator for PHP

Simple Validator is simple PHP library for validation purposes. It requires NO dependencies. It is inspired by [vlucas/valitron](https://github.com/vlucas/valitron). Following are the differences between the two:

- Style of defining rules is different. In [valitron](https://github.com/vlucas/valitron), it is done rules-wise whereas in this library it is done field wise which is more easier to manage.
- It has been written from the scratch while keeping minimum required PHP version as 7.1. It has resulted in better (and much less) code.
- Keep in mind, it is not exactly same as [valitron](https://github.com/vlucas/valitron) and contains comparitively less (and different) features.


## Requirements

Simple Validator requires PHP 7.1 or newer.

## Installation

You can install it using [composer](http://getcomposer.org)

```
composer require muizabdul29/simple-validator
```

## Examples

1. Basic Usage:

```php

use Simple\Validator;

// This may be $_POST variable or whatever
$data = [
    'name' => 'Abdul Muiz'
];

// This is how you define rules
$rules = [
    'name' => [ 'required', ['lengthBetween', 2, 16] ]
];

$v = new Validator($data);

if ($v->validate($rules)) {
    echo 'Valid';
} else {
    echo 'Invalid';
}

```

NOTE: The documentation is incomplete.