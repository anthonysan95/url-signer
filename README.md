# Create secured URLs with a limited lifetime

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anthonysan95/url-signer.svg?style=flat-square)](https://packagist.org/packages/anthonysan95/url-signer)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/anthonysan95/url-signer/master.svg?style=flat-square)](https://travis-ci.org/anthonysan95/url-signer)
[![Quality Score](https://img.shields.io/scrutinizer/g/anthonysan95/url-signer.svg?style=flat-square)](https://scrutinizer-ci.com/g/anthonysan95/url-signer)
[![Total Downloads](https://img.shields.io/packagist/dt/anthonysan95/url-signer.svg?style=flat-square)](https://packagist.org/packages/anthonysan95/url-signer)

This package can create URLs with or without a limited lifetime. This is done by adding an expiration date and a signature to the URL.

```php
$urlSigner = new UrlSigner();
$urlSigner->setKeyResolver(function() {
    return 'randomKey';
});

$signedUrl = $urlSigner->sign('https://myapp.com', [
    'api_token' => 'aisj2jifeji3i'
]);

echo $signedUrl;
// => The generated url will be always valid
// => This will output an URL that looks like 'https://myapp.com/?api_token=aisj2jifeji3i&signature=xxxx'.

$signedUrl = $urlSigner->temporarySign('https://myapp.com', 30);

echo $signedUrl;
// => The generated url will be valid for 30 days
// => This will output an URL that looks like 'https://myapp.com/?expires=xxxx&signeture=xxxx'
```

The signature will be generated using the `sha256` algorithm.

Imagine mailing this URL out to the users of your application. When a user clicks on a signed URL
your application can validate it with:

```php
$urlSigner->validate('https://myapp.com/?expires=xxxx&signature=xxxx');
```

## Installation

The package can installed via Composer:
```
composer require anthonysan95/url-signer
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please make an issue in the issue tracker.

## Credits

- [AnthonySan95](https://github.com/anthonysan95)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
