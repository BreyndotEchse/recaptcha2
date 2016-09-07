# reCAPTCHA 2.0 ("NoCaptcha") Module for Zend Framework 2 and Zend Framework 3

[![Build Status](https://travis-ci.org/BreyndotEchse/recaptcha2.svg?branch=master)](https://travis-ci.org/BreyndotEchse/recaptcha2)
[![Coverage Status](https://coveralls.io/repos/github/BreyndotEchse/recaptcha2/badge.svg?branch=master)](https://coveralls.io/github/BreyndotEchse/recaptcha2?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BreyndotEchse/recaptcha2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BreyndotEchse/recaptcha2/?branch=master)

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
php composer.phar require breyndotechse/recaptcha2
```

Then add `ReCaptcha2` to your `config/application.config.php`.

## Usage

### Basic Usage

```php
use ReCaptcha2\Captcha\ReCaptcha2;
use Zend\Form\Element;
use Zend\Form\Form;

//...
$form->add([
    'name' => 'captcha',
    'type' => Element\Captcha::class,
    'options' => [
        'captcha' => [
            'class' => ReCaptcha2::class,
            'options' => [
                'siteKey' => '<siteKey>',
                'secretKey' => '<secretKey>',
            ],
        ],
    ],
]);
```

### Advanced configuration

```php
use ReCaptcha2\Captcha\NoCaptchaService;
use ReCaptcha2\Captcha\ReCaptcha2;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Http\Client;

//...
$form->add([
    'name' => 'captcha',
    'type' => Element\Captcha::class,
    'options' => [
        'captcha' => [
            'class' => ReCaptcha2::class,                   // Required
            'options' => [
                // Required:
                'siteKey' => '<siteKey>',
                'secretKey' => '<secretKey>',
                // Optional:
                'service' => [
                    'class' => NoCaptchaService::class,     //Default = ReCaptcha2\Captcha\NoCaptchaService
                    'options' => [
                        'httpClient' => [
                            'class' => Client::class,       //Default = Zend\Http\Client
                            'options' => [
                                //Zend\Http\Client configuration
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
```

## SSL Issue / Google SSL Certificate

```
PHP Warning: fsockopen(): SSL operation failed with code 1. OpenSSL Error messages: error:14090086:SSL routines:SSL3_GET_SERVER_CERTIFICATE:certificate verify failed
```

Set the [openssl.cafile](http://php.net/manual/en/openssl.configuration.php) configuration setting in your [php.ini](http://php.net/manual/en/ini.core.php), .user.ini or .htaccess.
You can find a bundle of CA certificates here: [cURL CA Certificate Bundle](https://curl.haxx.se/ca/cacert.pem). This module comes with a cacert.pem file containing the "Google Internet Authority G2" and the "GeoTrust Global CA" CA certificate.

Otherwise set the `Zend\Http\Client` `sslcafile` configuration key:

```php
$form->add([
    'name' => 'captcha',
    'type' => Element\Captcha::class,
    'options' => [
        'captcha' => [
            'class' => ReCaptcha2::class,
            'options' => [
                'siteKey' => '<siteKey>',
                'secretKey' => '<secretKey>',
                'service' => [
                    'options' => [
                        'httpClient' => [
                            'options' => [
                                'sslcafile' => ReCaptcha2\Captcha\NoCaptchaService::CACERT_PATH,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
```

## Unit Tests

Coming soon