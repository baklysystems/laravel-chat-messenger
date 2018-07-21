<?php

namespace BaklySystems\LaravelMessenger;

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
            // assets.
            __DIR__.'/assets' => public_path('vendor/messenger'),
	    // routes
	    __DIR__.'/routes' => base_path('routes'),
        ]);

        // routes.
        $this->loadRoutesFrom(base_path('routes/messenger.php'));

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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Messenger Facede.
        $this->app->singleton('messenger', function () {
            return new Messenger;
        });
    }
}
