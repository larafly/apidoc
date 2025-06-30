<p align="center">
<a href="https://apidoc.pp-lang.tech"><img src="https://apidoc.pp-lang.tech/logo.png" width="400" alt="Laravel Apidoc"></a>
</p>

# Laravel Apidoc
## [中文文档](readme_zh_CN.md)

Laravel Apidoc is an API documentation generation tool based on [Laravel](https://laravel.com/).

## Installation

Install via Composer:

```bash
composer require larafly/apidoc
```

Run the following command to install the documentation tool:

```sh
php artisan apidoc:install
```

Now you can access `Laravel Apidoc` at `http://localhost:8000/apidoc` in your application.

## Configuration File

Publish the configuration file:

```sh
php artisan vendor:publish --tag=larafly-apidoc
```

Explanation of the `larafly-apidoc.php` file:

```php
<?php

return [
    // API documentation access route
    'route' => env('API_DOC_ROUTE', 'apidoc'),
    // Date format
    'datetime_format' => 'Y-m-d H:i:s',
    // Author of the API documentation
    'author' => env('GENERATOR_AUTHOR', 'system'),
    // Show documentation in production, default is false
    'is_show' => env('API_DOC_SHOW', false),
];
```

Set `GENERATOR_AUTHOR=Your Name` in your `.env` file to configure the author.

## Changelog

View the [changelog](changelog.md) for update history.

MIT. Please see the [license file](license.md) for more information.
