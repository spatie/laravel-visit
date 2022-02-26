# Quickly visit any route of your Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-visit.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-visit)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-visit/run-tests?label=tests)](https://github.com/spatie/laravel-visit/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-visit/Check%20&%20fix%20styling?label=code%20style)](https://github.com/spatie/laravel-visit/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-visit.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-visit)

This package contains an artisan command `visit` that allows you to visit any route of your Laravel app. 

```bash
php artisan visit /my-page
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

Optionally, you can publish the config file.

```bash
php artisan vendor:publish --tag="visit-config"
```

This is the content of the published config file:

```php
return [
    /*
     * These classes are responsible for colorizing the output.
     */
    'colorizers' => [
        Spatie\Visit\Colorizers\JsonColorizer::class,
        Spatie\Visit\Colorizers\HtmlColorizer::class,
    ],

    /*
     * These stats will be displayed in the response block.
     */
    'stats' => [
        ...Spatie\Visit\Stats\DefaultStatsClasses::all(),
    ]
];
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

**This feature does not work reliable on Windows**

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

![screenshot](https://spatie.github.io/laravel-visit/images/headers.png)

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

To avoid displaying the response, and only display the response result block, use the `--only-stats` option

```bash
php artisan visit / --only-stats
```

### Avoid colorizing the response

The `visit` command will automatically colorize any HTML and JSON output. To avoid the output being colorized, use the `--no-color` option.

```bash
php artisan visit / --no-color
```

### Displaying the result HTML as text

Usually an HTML response is quite lengthy. This can make it hard to quickly see what text will be displayed in the browser. To convert an HTML to a text variant, you can pass the `--as-text` option.

```bash
php artisan visit / --as-text
```

This is how the default Laravel homepage will look like.

![screenshot](TODO: insert screenshot)

### Filtering HTML output

If you only want to see a part of an HTML response you can use the `--filter` option. For HTML output, you can pass [a css selector](https://www.w3schools.com/cssref/css_selectors.asp).

Imagine that your app's full response is this HTML:

```html
<html>
    <body>
        <div>First div</div>
        <p>First paragraph</p>
        <p>Second paragraph</p>
    </body>
</html>
```

This command ...

```bash
php artisan visit / --filter="p"
```

... will display:

```html
<p>First paragraph</p>
<p>Second paragraph</p>
```

### Filtering JSON output

If you only want to see a part of an JSON response you can use the `--filter` option. You may use dot-notation to reach nested parts.

Imagine that your app's full response is this JSON:

```json
{
    "firstName": "firstValue",
    "nested": {
        "secondName": "secondValue"
    }
}
```

This command ...

```bash
php artisan visit / --filter="nested.secondName"
```

... will display:

```html
secondValue
```

### Adding stats

In the results block underneath the response, you'll see a few interesting stats by default, such as the response time and queries executed.

You can add more stats there by creating your own `Stat` class. A valid `Stat` is any class that extends `Spatie\Visit\Stats\Stat`. 

Here's how that base class looks like:

```php
namespace Spatie\Visit\Stats;

use Illuminate\Contracts\Foundation\Application;

abstract class Stat
{
    public function beforeRequest(Application $app)
    {
    }

    public function afterRequest(Application $app)
    {
    }

    abstract public function getStatResult(): StatResult;
}
```

As an example implementation, take a look at the `RunTimeStat` that ships with the package.

```php
namespace Spatie\Visit\Stats;

use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class RuntimeStat extends Stat
{
    protected Stopwatch $stopwatch;

    protected ?StopwatchEvent $stopwatchEvent = null;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch(true);
    }

    public function beforeRequest(Application $app)
    {
        $this->stopwatch->start('default');
    }

    public function afterRequest(Application $app)
    {
        $this->stopwatchEvent = $this->stopwatch->stop('default');
    }

    public function getStatResult(): StatResult
    {
        $duration = $this->stopwatchEvent->getDuration();

        return StatResult::make('Duration')
            ->value($duration . 'ms');
    }
}
```

To activate a `Stat`, you should add its class name to the `stats` key of the `visit` config file.

```php
// in config/stats.php

return [
    // ...
    
    'stats' => [
        App\Support\YourCustomStat::class,
        ...Spatie\Visit\Stats\DefaultStatsClasses::all(),
    ]
]
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
