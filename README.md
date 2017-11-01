# Laravel Chat Messenger
A Laravel JQuery chat that is just like Facebook chat

## Installation

Laravel Messenger supports Laravel 5.4 and higher.

### Package

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

### Laravel Messenger Files

Then, run `php artisan vendor:publish` to publish the config file, MessageController and assets.

### Laravel Messenger Sytles and Scripts

Make sure to add `@yield('css-styles')` in your app/master head section and `@yield('js-scripts')` to your app/master scripts section, or edit section naming in `view/vendor/messenger/messenger.blade.php`

JQuery is required for the messenger script.

### Laravel Messenger Pusher

Add your pusher keys in `config/messenger.php` file.

And voila, you can start conversation with any user by linking to `your-domain.com/messenger/t/{userId}`.

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
