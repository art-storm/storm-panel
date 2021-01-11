
<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Storm Panel

Laravel Admin Panel

This admin panel realised on Laravel 7.

This admin panel will be useful for those who are starting to develop a new project and who need functions that have already been implemented in this admin panel.
<h5>Realised features</h5>
- User registration
- User authenticate
- Two factor auth
- User management
- User role management
- Menu builder

## Installation

Create a project folder, move to it and clone the repository
```
git clone https://github.com/art-storm/storm-panel.git .
``` 

Install packages by composer
```
composer install
```

After installing, you may need to configure some permissions. Directories within the *storage* and the *bootstrap/cache* directories should be writable by your web server or Laravel will not run.

Create mysql database, then copy .env.example to .env and update mysql connection parameters.

Set your application key
```
php artisan key:generate
```

Run database migrations
```
php artisan migrate
```

Run database seeders
```
php artisan db:seed
```

That's all, now you can use it.

Admin login credentials:
>**Email:** `admin@admin.com`   
>**Password:** `password`


## Security Vulnerabilities

If you discover a security vulnerability within Storm Panel, please let's me know.

## License
You can use it anywhere and however you want.

It will be great if it helps you.

The Storm Panel is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
