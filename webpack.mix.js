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
    .vue()
    .copy('resources/css/style.css', 'public/css').version();

mix.copy('resources/fonts', 'public/fonts')
    .copy('resources/css/materialdesignicons.min.css', 'public/css')
    .copy('resources/vendors', 'public/vendors')
    .copy('resources/img', 'public/img')
    .copy('resources/js/vendor.bundle.base.js', 'public/js')
    .copy('resources/js/misc.js', 'public/js')
    .copy('resources/js/hoverable-collapse.js', 'public/js');
