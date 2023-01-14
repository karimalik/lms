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
var backend_plugin_js = 'resources/js/backend/';

mix
    .options({
        processCssUrls: false
    })
    .js([
        'resources/js/app.js'
    ], 'public/js/common.js')

    .sass('resources/sass/backend/app.scss', 'public/backend/css/app.css')
    .sass('resources/sass/backend/backend_style.scss', 'public/backend/css/backend_style.css')
    .sass('resources/sass/backend/backend_style_rtl.scss', 'public/backend/css/backend_style_rtl.css')
    .combine([
        backend_plugin_js + 'treeview.js',
        backend_plugin_js + 'plugin.js',
        backend_plugin_js + 'jquery.data-tables.js',
        backend_plugin_js + 'dataTables.buttons.min.js',
        backend_plugin_js + 'buttons.flash.min.js',
        backend_plugin_js + 'jszip.min.js',
        backend_plugin_js + 'pdfmake.min.js',
        backend_plugin_js + 'vfs_fonts.min.js',
        backend_plugin_js + 'buttons.html5.min.js',
        backend_plugin_js + 'buttons.print.min.js',
        backend_plugin_js + 'dataTables.rowReorder.min.js',
        backend_plugin_js + 'dataTables.responsive.min.js',
        backend_plugin_js + 'buttons.colVis.min.js',
        backend_plugin_js + 'nice-select.min.js',
        backend_plugin_js + 'jquery.magnific-popup.min.js',
        backend_plugin_js + 'fastselect.standalone.min.js',
        backend_plugin_js + 'moment.min.js',
        backend_plugin_js + 'bootstrap-datetimepicker.min.js',
        backend_plugin_js + 'bootstrap-datepicker.min.js',
        'public/backend/js/summernote-bs4.min.js',
        backend_plugin_js + 'metisMenu.min.js',
        backend_plugin_js + 'circle-progress.min.js',
        backend_plugin_js + 'colorpicker.min.js',
        backend_plugin_js + 'colorpicker_script.js',
        backend_plugin_js + 'jquery.validate.min.js',
        backend_plugin_js + 'main.js',
        backend_plugin_js + 'custom.js',
        backend_plugin_js + 'footer.js',
        backend_plugin_js + 'developer.js',
        backend_plugin_js + 'select2.min.js',
        backend_plugin_js + 'backend.js',
        backend_plugin_js + 'search.js',
        backend_plugin_js + 'filepond.min.js',
        backend_plugin_js + 'filepond-plugin-file-validate-type.js',
        backend_plugin_js + 'filepond-plugin-image-preview.min.js',
        backend_plugin_js + 'filepond.jquery.js',
        backend_plugin_js + 'jquery.multiselect.js',
    ], 'public/backend/js/plugin.js')
;

var front_default_plugin_js = 'resources/js/frontend/default/';
mix.js([
    front_default_plugin_js + 'owl.carousel.min.js',
    front_default_plugin_js + 'waypoints.min.js',
    front_default_plugin_js + 'jquery.counterup.min.js',
    front_default_plugin_js + 'wow.min.js',
    front_default_plugin_js + 'jquery.slicknav.js',
    'public/backend/js/summernote-bs4.min.js',
    backend_plugin_js + 'nice-select.min.js',
    front_default_plugin_js + 'mail-script.js',
    front_default_plugin_js + 'jquery.lazy.min.js',
    front_default_plugin_js + 'main.js',
    front_default_plugin_js + 'footer.js'
], 'public/frontend/infixlmstheme/js/app.js')
    .sass('resources/sass/frontend/default/app.scss', 'public/frontend/infixlmstheme/css/app.css')
    .sass('resources/sass/frontend/default/frontend_style.scss', 'public/frontend/infixlmstheme/css/frontend_style.css')
;

mix.js('resources/js/chat.js', 'public/js/app.js');
