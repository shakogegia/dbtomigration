<?php

namespace Shakogegia\Dbtomigration;

use Illuminate\Support\ServiceProvider;

class DbtomigrationServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Shakogegia\Dbtomigration\Commands\DbToMigrationCommand'
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish a config file
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
