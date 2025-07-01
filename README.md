<p align="center">
<a href="https://apidoc.pp-lang.tech"><img src="https://apidoc.pp-lang.tech/logo.png" width="400" alt="Laravel Apidoc"></a>
</p>

# Laravel Apidoc
## [中文文档](README_zh_CN.md)

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

### Generate Request

Use the command line to generate a Request class:

```sh
php artisan apidoc:request UserRequest
```

If your class extends the pagination base class `PageApiRequest`, you can add the `--p` option:

```sh
php artisan apidoc:request UserRequest --p
```

### Generate Response

Use the command line to generate a Response class:

```sh
php artisan apidoc:response UserResponse
```

If your class extends the paginated response class `PaginateResponse`, you can add the `--p` option:

```sh
php artisan apidoc:response UserResponse --p
```

### Generation Commands

1. Write documentation to the database. Run the following command:

```shell
php artisan apidoc
```

After generation, visit `http://localhost:8000/apidoc` to view the documentation.
If the generated result is incorrect, check whether the API configuration is properly defined.

2. Write documentation to a `markdown` file. Run the following command:

```shell
php artisan apidoc:md
```

After generation, go to `storage/app/public/apidoc` to view the generated documentation files.

## Changelog

View the [changelog](https://apidoc.pp-lang.tech/en/guide/start/changelog.html) for update history.

MIT. Please see the [license file](license.md) for more information.
