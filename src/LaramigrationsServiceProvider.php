<?php

namespace Shakogegia\Laramigrations;

use Illuminate\Support\ServiceProvider;

class LaramigrationsServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Shakogegia\Laramigrations\Commands\SqlToMigrationCommand'
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish a config file
        // $this->publishes([

        //     __DIR__.'/config/laramigrations.php' => config_path('laramigrations.php'),
        // ]);
        // 
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
