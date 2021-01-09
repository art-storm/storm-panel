const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .extract(['vue', 'jquery', 'axios', 'bootstrap', 'bootbox', 'popper.js', 'datatables.net',
        'datatables.net-bs4', 'datatables.net-responsive-bs4', 'nestable2'])
    .sass('resources/sass/app.scss', 'public/css');

mix.scripts([
    'resources/js/script.js',
], 'public/js/script.js');