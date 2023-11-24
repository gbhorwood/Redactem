# Redactem
[![License](http://poser.pugx.org/gbhorwood/redactem/license)](https://packagist.org/packages/gbhorwood/redactem)
[![Version](http://poser.pugx.org/gbhorwood/redactem/version)](https://packagist.org/packages/gbhorwood/redactem)
[![PHP Version Require](http://poser.pugx.org/gbhorwood/redactem/require/php)](https://packagist.org/packages/gbhorwood/redactem)


Redactem is php package for redacting values from json. If you need to scrub out passwords or credit card numbers, or the value of any key/value pair identified by key or regular expression, from a string of json before writing it to disk or db, Redactem can do that.

Redactem was developed as an internal-use tool for fruitbat studios/cloverhitch technologies/kludgetastic implementations.

## Install
Redactem is installed via composer:

```shell
composer require gbhorwood/redactem
```

Then, in your script, require and use:

```php
require __DIR__ . '/vendor/autoload.php';

use Gbhorwood\Redactem\Redact;
```

## Features
Redactem finds and replaces values in json with a chosen or default redaction text. Replacements are done to an arbitrary depth in nested strucutres. Valid json stored as a string is also processed.

Finding values to redact can be done by key name, case sensitive or insensitive, or by regular expression matches on values. Redactem offers convenience methods to redact passwords, credit card number, or emails, but also allows you to write your own rulesets.

Before basic password redaction:
```json
{
  "password": "redactme",
  "somearray": {
    "password": "redactme"
  },
  "somestring": "{\"password\": \"redactme\"}"
}
```

After:
```json
{
  "password": "*****",
  "somearray": {
    "password": "*****"
  },
  "somestring": "{\"password\":\"*****\"}"
}
```

## Redacting passwords
Redacting passwords can be done with the `passwords()` convenience method:

```php
$redactedJson = \Gbhorwood\Redact\Redact::passwords($originalJson);
```

This method redacts all values keyed with the following:

* `pwd`
* `pass`
* `psswd`
* `password`
* `pwd_repeat`
* `repeat_pwd`
* `pass_repeat`
* `repeat_pass`
* `passwd_repeat`
* `repeat_passwd`
* `password_repeat`
* `repeat_password`

If you have password data that is keyed with a different key than listed, you can use the `byKey()` method.

Password values are replaced by default with '`*****`' (five asterisks). If you would like to set a custom redaction text, `passwords()` takes a redaction text as an optional second argument.

```php
$redactedJson = \Gbhorwood\Redact\Redact::passwords($originalJson, 'REDACTED');
```

## Redacting credit card numbers
Redacting credit card numbers can be done with the `creditcards()` convenience method:

```php
$redactedJson = \Gbhorwood\Redact\Redact::creditcards($originalJson);
```

Credit card numbers are identified by a regular expression that matches major credit card vendor's patterns. 

Credit card values are replaced with a redaction text of asterisks the length of the credit card number.  If you would like to set a custom redaction text, `creditcards()` takes a redaction text as an optional second argument.

```php
$redactedJson = \Gbhorwood\Redact\Redact::creditcards($originalJson, 'REDACTED');
```

## Redacting emails
Redacting email addresses can be done with the `emails()` convenience method:

```php
$redactedJson = \Gbhorwood\Redact\Redact::emails($originalJson);
```

Email address are matched using php's `filter_var()` function with `FILTER_VALIDATE_EMAIL`.

Email values are replaced with a partial redaction of the email, allowing it to be recognized by readers that already know the email address. For instance, the email address `gbhorwood@example.ca` would be redacted as `gb*****od@ex***le.ca`. If you would like to set a custom redaction text, `emails()` takes a redaction text as an optional second argument.

```php
$redactedJson = \Gbhorwood\Redact\Redact::emails($originalJson, 'REDACTED');
```

## Custom redactions by key
Redactions can be done by specifying a key of a key/value pair to redact:

```php
$redactedJson = \Gbhorwood\Redact\Redact::byKey($originalJson, 'somekey');
```

By default, keys are treated as _case insensitive_. If you wish to enable case sensitivity, pass true as an optional third argument

```php
$redactedJson = \Gbhorwood\Redact\Redact::byKey($originalJson, 'SomeKey', true);
```

The default behaviour is to redact values with the default redaction text of `*****` (five asterisks). A custom redaction text can be supplied as an optional fourth argument

```php
$redactedJson = \Gbhorwood\Redact\Redact::byKey($originalJson, 'somekey', true, 'REDACTED');
```

Redactions can be done for multiple keys by calling `byKeys` and passing an array of keys:

```php
$redactedJson = \Gbhorwood\Redact\Redact::byKeys($originalJson, ['somekey', 'otherkey'], true, 'REDACTED');
```

The `byKeys` method behaves identically to the `byKey` method, the only difference being the second argument is an array of keys.

## Custom redactions by regex
Redactions can be done by supplying a regular expression to match values to redact

```php
$redactedJson = \Gbhorwood\Redact\Redact::byRegex($originalJson, '/someregex/');
```

Regular expressions supplied to `byRegex()` are matched against values using php's `preg_match()`.

The default behaviour is to redact values with the default redaction text of `*****` (five asterisks). A custom redaction text can be supplied as an optional third argument

```php
$redactedJson = \Gbhorwood\Redact\Redact::byRegex($originalJson, '/someregex/', 'REDACTED');
```

## Writing custom redaction rules
Redactem's behaviour can be customized by using the base function `redact()`.

The `redact()` function accepts three arguments:

* `$json` A string of the json to redact
* `$shouldRedact` A callable that accepts two arguments, the `$key` and the `$value` of the key/value pair to test for redaction, and returns a boolean. A return value of `true` means the value should be redacted.
* `$redactionText` A callable that accepts one argument, the `$value` of the key/value pair, and returns a string. The returned string is the redaction text to put in place of the value.

A usage example is:

```php
/**
 * A function to test if a key/value pair should be redacted
 * Here, if the key is equal to 'secret' and the value is only digits.
 * @param  String|Int $k The key of a key/value pair
 * @param  String|Int $v The value of a key/value pair
 * @return bool
 */
$shouldRedact = fn ($k, $v) => (bool)($k == 'secret' && preg_match('/^[0-9]*$/', (string)$v));

/**
 * A function to build the redaction text for a value
 * Here, a string of exclamation marks the length of the string being redacted
 * @param  String|Int $v The value of a key/value pair
 * @return String
 */
$redactionText = fn ($v) => join(array_fill(0, strlen((string)$v), '!'));

$redactedJson = Redact::redact($originalJson, $shouldRedact, $redactionText);
```

This example will accept input of json that looks like:

```json
{
    "name": "jasvinder",
    "secret": "astring",
    "somearray": {
        "secret": 1234
    }
}
```

And return:

```json
{
    "name": "jasvinder",
    "secret": "astring",
    "somearray": {
        "secret": "!!!!"
    }
}
```
