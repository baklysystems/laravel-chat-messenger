# Laravel Chat Messenger
A Laravel JQuery chat that is just like Facebook chat

## Installation

Laravel Messenger supports Laravel 5.4 and higher.

Require via composer

```bash
$ composer require baklysystems/laravel-chat-messenger
```

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

And voila, you can start conversation with any user by linking to [your-domain.com/messenger/t/{userId}]().

## Customization

### Migrations

To publish and edit messenger migrations, run the publish command with `messenger-migrations` tag.

```bash
$ php artisan vendor:publish --tag messenger-migrations
```
### Views

To publish and edit messenger views, run the publish command with `messenger-views` tag.

```bash
$ php artisan vendor:publish --tag messenger-views
```

## TODO

1. Support Laravel 5.*
2. seen and unseen messages.
3. emotions.
4. upload photos.
5. Attach files.
6. User can delete messages.
7. Show date before every conversation beginning.
8. paginate and load threads.
9. Laravel Messenger chatbox.
10. Unauthenticated chatbox to message customer service.
