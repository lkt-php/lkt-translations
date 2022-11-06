# LKT Translations

## Installation

```shell
composer require lkt/translations
```

## Usage

### Translations folder and translations files

Languages are declared by registering a language folder. This is a directory in you project holding as many php files as you want.

Each translation file must return an array.

An example could be:

```shell
- /
- - /translations
- - - /en
- - - - users.php
- - - - ...
- - - /es
- - - - users.php
- - - - ...
- - - /it
- - - - users.php
- - - - ...
```

English version of users.php could be:

```php
<?php

return [
    'name' => 'Name',
    'billing' => [
        'address' => 'Billing address',
        'city' => 'Billing city',
    ]
];
```

### Register translations in your code

Each directory can be loaded with `addLocalePath` method:

```php
use Lkt\Translations\Translations;

Translations::addLocalePath('en', __DIR__ . '/translations/en');
Translations::addLocalePath('es', __DIR__ . '/translations/es');
Translations::addLocalePath('it', __DIR__ . '/translations/it');
Translations::addLocalePath('fr', __DIR__ . '/translations/fr');
```

### Retrieve all available languages
```php
use Lkt\Translations\Translations;

$languages = Translations::getAvailableLanguages(); // An array => ['en', 'es', 'it', 'fr]
```

### Access to translations

```php
use Lkt\Translations\Translations;

// Get translations with current language
Translations::get('name');

// Nested translations can be accessed using the dot separator:
Translations::get('billing.address');

// Specify wanted language
Translations::get('name', 'it');
```

### Current language

By default, the language used it's the first registered when translations directories was loaded.

If you want to, it can be updated at any time this way:

```php
use Lkt\Translations\Translations;

Translations::setLang('es');
```

## Additional features

### Find missed translations between languages

Sometimes can be hard to maintain translations files and there is an useful method for that purpose:

```php
use Lkt\Translations\Translations;

$missed = Translations::getMissedTranslations()
```

This method will return an array for each language with all the keys without a translation.

For example:

```php
[
    'en' => [
        'sayHello' => ''
    ],
    'es' => [
        'sayHello' => 'Hi'
    ]
]
```

### Find translations with the same value between languages

It's time to detect if there are translations declared but not translated yet.

```php
use Lkt\Translations\Translations;

$missed = Translations::getTranslationsNotTranslated()
```

This method will return an array for each language with a minimum of one key with the same value in other language.

For example:

```php
[
    'en' => [
        'lorem' => 'ipsum',
        'dolor.sit.amet' => 'Amet'
    ],
    'es' => [
        'lorem' => 'ipsum',
        'dolor.sit.amet' => 'Amet'
    ]
]
```

### Export translations

```php
use Lkt\Translations\Translations;

$exported = Translations::export();

// $exported content:
[
    "en" => [
        "lorem" => "ipsum",
        "dolor.sit.amet" => "Dolor sit amet",
    ],
    "fr" => [
        "lorem" => "ipsum",
        "dolor.sit.amet" => "Dolor sit amet fr",
        "langExclusiveTranslation" => "Hi"
    ]
];
```