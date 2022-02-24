# Quickly visit any route of your Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-visit.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-visit)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-visit/run-tests?label=tests)](https://github.com/spatie/laravel-visit/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-visit/Check%20&%20fix%20styling?label=code%20style)](https://github.com/spatie/laravel-visit/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-visit.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-visit)

This package contains an artisan command `visit` that allows you to visit any route of your Laravel app. 

```bash
php artisan /my-page
```

The command display the colorized version of the HTML...

![screenshot](https://spatie.github.io/laravel-visit/images/html.png)

... followed by a results block.

![screenshot](https://spatie.github.io/laravel-visit/images/results.png)

The command can also colorize JSON output. It also has support for some Laravel niceties such as logging in users before making a request, using a route name instead of and URL, and much more.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-visit.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-visit)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-visit
```

## Usage

To visit a certain page, execute `php artisan` followed by a URL.

```bash
php artisan visit /your-page
```

![screenshot](https://spatie.github.io/laravel-visit/images/html.png)

Instead of passing an URL, you can pass a route name to the `route` option. Here's an example where we will visit the route named "contact".

```bash
php artisan visit --route=contact
```

### Using a different method

By default, the `visit` command will make GET request. To use a different HTTP verb, you can pass it to the `method` option.

```bash
php artisan visit /users/1 --method=delete
```

### Passing a payload

You can pass a payload to non-GET request by using the payload. The payload should be formatted as JSON.

```bash
php artisan visit /users --method=post --payload='{"testKey":"testValue"}'
```

### Logging in a user

To log in a user before making a request, add the `--user` and pass it a user id.

```php
php artisan visit /api/user/me --user=1
```

Alternatively, you can also pass an email address to the `user` option.

```php
php artisan visit /api/user/me --user=john@example.com
```

### Showing the headers of the response

By default, the `visit` command will not show any headers. To display them, add the `--show-headers` option

```bash
php artisan visit /my-page --show-headers
```

![screenshot](TODO: add screenshot)

### Showing exception pages

When your application responds with an exception, the `visit` command will show the html of the error page.

To let the `visit` command display the actual exception, use the `--show-exception` option.

```bash
php artisan visit /page-with-exception --show-exception
```

### Only displaying the response

If you want the `visit` command to only display the response, omitting the response result block at the end, pass the `--only-response` option.

```bash
php artisan visit / --only-response
```

### Only displaying the response properties block

To avoid displaying the response, and only display the response result block, use the `--only-response-properties` option

```bash
php artisan visit / --only-response-properties
```

### Avoid colorizing the response

The `visit` command will automatically colorize any HTML and JSON output. To avoid the output being colorized, use the `--no-color` option.

```bash
php artisan visit / --no-color
```



## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
