# laramigrations
Laravel 5.3 package to convert database to laravel migartions

## Install
```
composer require shakogegia/laramigrations
```

Add Service Provider to config/app.php in providers section
```
Shakogegia\Laramigrations\LaramigrationsServiceProvider::class
```

# Usage
Run artisan command to generate migration files
```
php artisan sqltomigration:generate
```
