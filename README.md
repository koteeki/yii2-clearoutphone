Global Phone Number Validation Service using ClearoutPhone's API 
===
A Yii2 extension to use the ClearoutPhone API. Supported countries:
https://clearoutphone.io/supported-countries/

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run:

```
php composer.phar require --prefer-dist "koteeki/yii2-clearoutphone":"@dev"
```

or add:

```
"koteeki/yii2-clearoutphone": "@dev"
```

to the `require` section of your `composer.json` file.


Usage
-----
* Get ClearoutPhone API token: https://clearoutphone.io/
* Add the following code to your application configuration:
```php
return [
    'components' => [
        'clearoutphone' => [
            'class' => \koteeki\clearoutphone\ClearoutPhone::class,
            'token' => 'YOUR_TOKEN_HERE',
            'timeout' => 5000,
        ],
    ],
];
```
* You can now access the extension via ```\Yii::$app->clearoutphone```

Methods
-------
* **`getCredits()`** - get to know the available credits.
  
* **`getPhoneDetails(string $number, string $countryCode = null)`** - validates phone the number, returns `PhoneNumberDetails`.
 
Exceptions
-------
The methods above throw exception due to a phone number validation failed:

* `BadRequestException` - validation failed due to a request error,
* `PaymentRequiredException` - you have exhausted your credits,
* `ServiceUnavailable` - the service is unavailable,
* `TimeoutException` - timeout occurred,
* `UnauthorizedException` - token is invalid,
* `ClearoutPhoneException` - an unknown error.

Example
-------
```php
use koteeki\clearoutphone\ClearoutPhone;
use koteeki\clearoutphone\exceptions\ClearoutPhoneException;

try {
    /** @var ClearoutPhone $clearout */ 
    $clearout = \Yii::$app->clearoutphone;
    $phoneDetails = $clearout->getPhoneDetails('+13024401582');
    if ($phoneDetails->isValid) {
        // your magic here
    }
} catch (ClearoutPhoneException $e) {
    Yii::error($e->getMessage());
}
```