# laravel-api-key
API Key Authorization for Laravel with replay attack prevention

## Installation

```bash
$ composer require mxl/laravel-api-key
```

## How it works?

Both sides (i.e. client and server) have a secret key.
Client calculates a token - hash value for concatenated secret key and current timestamp.
The Token and the timestamp are sent with request to server as separate HTTP headers.
Server recalculates hash value and validates the token by comparing it with this value and by checking that received timestamp belongs to current time ± window interval.

## Configuration

Package uses default configuration from `vendor/laravel-api-key/config/apiKey.php`:
```php
<?php

return [
    'secret' => env('API_KEY_SECRET'),
    'hash' => env('API_KEY_HASH', 'md5'),
    'timestampHeader' => env('API_KEY_TIMESTAMP_HEADER', 'X-Timestamp'),
    'tokenHeader' => env('API_KEY_TOKEN_HEADER', 'X-Authorization'),
    'window' => env('API_KEY_WINDOW', 30),
];
```

To change it set environment variables mentioned in this configuration or copy it to your project with:

```bash
$ php artisan vendor:publish --provider="MichaelLedin\LaravelApiKey\ApiKeyServiceProvider" --tag=config
```

and modify `config/apiKey.php` file.

The configuration has following parameters:
- `secret` - secret key that is known by client and server;
- `hash` - an algorithm used to create hash value from secret key and timestamp; for a list of supported algorithms check an output of [hash_algos](https://www.php.net/manual/en/function.hash-algos.php) function;
- `timestampHeader` - HTTP header used to pass a timestamp;
- `tokenHeader` - HTTP header used to pass a token;
- `window` - window interval, in seconds;

## Usage

Assign the middleware to routes using middleware class name:

```php
use \MichaelLedin\LaravelApiKey\AuthorizeApiKey;

Route::get('admin/profile', function () {
    //
})->middleware(AuthorizeApiKey::class);
```

or an alias:

```php
Route::get('admin/profile', function () {
    //
})->middleware('apiKey');
```

## Maintainers

- [@mxl](https://github.com/mxl)

## Other useful Laravel packages from the author

- [mxl/laravel-queue-rate-limit](https://github.com/mxl/laravel-queue-rate-limit) - simple Laravel queue rate limiting;
- [mxl/laravel-job](https://github.com/mxl/laravel-job) - dispatch a job from command line and more;

## License

See the [LICENSE](https://github.com/mxl/laravel-api-key/blob/master/LICENSE) file for details.


