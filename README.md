# Laravel Chat Messenger
A Laravel JQuery chat that is just like Facebook chat

## Installation

require via composer `composer require baklysystems/laravel-chat-messenger`

In `config/app.php` file

```php
'providers' => [
    ...
    BaklySystems\LaravelMessenger\LaravelMessengerServiceProvider::class,
    ...
];

'aliases' => [
    ...
    'Messenger' => BaklySystems\LaravelMessenger\Facades\Messenger::class,
    ...
];
```
Then, run `php artisan vendor:publish` to publish the config file, controller and assets.
