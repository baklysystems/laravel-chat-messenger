<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelMessengerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // config file.
            __DIR__.'/config/messenger.php' => config_path('messenger.php'),
            // controller.
            __DIR__.'/Http/Controllers/MessageController.php'
                => app_path('Http/Controllers/MessageController.php'),
        ]);

        // routes.
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');

        // migrations.
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        // publish under messenger-migrations tag.
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'messenger-migrations');

        // views.
        $this->loadViewsFrom(__DIR__.'/views', 'messenger');
        // publish under messenger-views tag.
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/messenger'),
        ], 'messenger-views');

        // assets.
        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/messenger'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
