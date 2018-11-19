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
    .scripts([
        'resources/js/products/search.js',
        "resources/js/stock/upload.js",
        "resources/js/jobs/upload.js",
        "resources/js/jobs/job.js",
        "resources/js/search.js",
        "resources/js/editor.js",
        "resources/js/models/*.js",

    ], "public/js/scripts.js")
    .sass('resources/sass/app.scss', 'public/css');

