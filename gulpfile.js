process.env.DISABLE_NOTIFIER = true;

const gulp = require('gulp');
const dotenv  = require('dotenv');
const config = dotenv.config();
const less = require('gulp-less');
const modifyFile = require('gulp-modify-file');
const modifyCSSURLS = require('gulp-modify-css-urls');
const cssmin = require('gulp-cssmin');
const uglify = require('gulp-uglify');
const awspublish = require('gulp-awspublish');
const rename = require('gulp-rename');
const del = require('del');

const elixir = require('laravel-elixir');
const Task = elixir.Task;

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */


elixir.extend('delete', function(paths) {
    new Task('delete', function() {
        return del(paths);
    });

});

elixir.extend('appConfigJS', function (source, dest) {
    new Task('appConfigJS', function () {
        gulp.src(source)
            .pipe(
                modifyFile((content, path, file) => {

                    var configs = {
                        'APP_URL' : config['APP_URL'],
                        'SOCKET_URL' : config['SOCKET_URL'],
                        'SOCKET_ONLINE_CHANNEL' : config['SOCKET_ONLINE_CHANNEL'],
                        'MONEY_PRECISION' : config['MONEY_PRECISION']
                    }

                    eval(content);

                    for(var c in configs){
                        app[c.toLowerCase()] = configs[c];
                    }

                    return 'var app = ' + JSON.stringify(app);
                })
            )
            .pipe(gulp.dest(dest));

    })
});

elixir.extend('appLESS', function (source, dest) {

    new Task('appLESS', function () {

        var g = gulp.src(source).pipe(less());

        g = g.pipe(modifyCSSURLS({
            prepend: config['APP_CDN_URL']
        }))

        if(elixir.config.production){
            g = g.pipe(cssmin());
        }

        g.pipe(gulp.dest(dest));

    }).watch(source);

});

elixir.extend('appJS', function (source, dest) {

    new Task('appJS', function () {
        var g = gulp.src(source)

        if(elixir.config.production){
            g = g.pipe(uglify());
        }


        g.pipe(gulp.dest(dest));

    }).watch(source);

});

elixir.extend('aws', function (source, prefix) {

    new Task('aws', function () {

        var publisher = awspublish.create({
            accessKeyId: config['AWS_S3_ACCESS_KEY_ID'],
            secretAccessKey: config['AWS_S3_SECRET_ACCESS_KEY'],
            region: config['AWS_S3_REGION_ID'],
            params: {
                Bucket: config['AWS_S3_BUCKET']
            },
            'signatureVersion': 'v3'
        });

        var headers = {
            'Cache-Control': 'public,max-age=' + config['AWS_S3_MAX_AGE']
        };


        var g = gulp.src(source)

        if(prefix) {

            g = g.pipe(rename(function (path) {
                path.dirname = prefix + path.dirname;
            }))
        }

        g.pipe(awspublish.gzip())
            .pipe(publisher.publish(headers))
            .pipe(publisher.sync(prefix))
            .pipe(publisher.cache())
            .pipe(awspublish.reporter());

    });

});

elixir(function(mix) {

    var assets_source_path = 'resources/assets';
    var assets_public_path = 'public';

    var func = {

        path : {
            assets : function(source){

                return './' + assets_source_path + source;
            },

            public : function(source){
                return assets_public_path + source;
            }

        }

    };

    mix
    //.delete(['public/build'])
        .copy([
            func.path.assets('/vendor/jquery-textext/src/css/*.png')
        ], func.path.public('/build/vendor/css/jquery-textext'))
        .copy([
            func.path.assets('/vendor/font-awesome/fonts'),
            func.path.assets('/vendor/bootstrap/dist/fonts')
        ], func.path.public('/build/fonts'))
        .copy([
            func.path.assets('/vendor/jquery-ui/themes/smoothness/images'),
            func.path.assets('/vendor/images-grid/src/img')
        ], func.path.public('/build/css/images'))
        .webpack([
            func.path.assets('/js/sockets/*.js'),
        ], func.path.public('/js/sockets/all.js'), null, {'watch' : false})
        .styles([
            func.path.assets('/vendor/jquery-textext/src/css/*.css'),
        ], func.path.public('/vendor/css/jquery-textext/all.css'))
        .styles([
            func.path.assets('/vendor/bootstrap/dist/css/bootstrap.css'),
            func.path.assets('/vendor/font-awesome/css/font-awesome.css'),
            func.path.assets('/vendor/jquery-ui/themes/smoothness/jquery-ui.css'),
            func.path.assets('/vendor/bootstrap-toggle/css/bootstrap-toggle.css'),
            func.path.assets('/vendor/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.css'),
            func.path.assets('/vendor/animate.css/animate.css'),
            func.path.assets('/vendor/jquery.business-hours/jquery.businessHours.css'),
            func.path.assets('/vendor/jt.timepicker/jquery.timepicker.css'),
            func.path.assets('/vendor/simplelightbox/dist/simplelightbox.css'),
            func.path.assets('/vendor/images-grid/src/images-grid.css'),
            func.path.assets('/vendor/Croppie/croppie.css')
        ], func.path.public('/css/vendor.css'))
        .less([
            func.path.assets('/less/layouts/smart.less'),
        ], func.path.public('/css/app.css'))
        .scripts([
            func.path.assets('/vendor/jquery-textext/src/js/textext.core.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.ajax.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.arrow.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.clear.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.filter.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.focus.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.prompt.js'),
            func.path.assets('/vendor/jquery-textext/src/js/textext.plugin.suggestions.js'),
            func.path.assets('/js/vendor/jquery-textext/src/js/textext.plugin.tags.js'),
            func.path.assets('/js/vendor/jquery-textext/src/js/textext.plugin.autocomplete.js'),
        ], func.path.public('/vendor/js/jquery-textext/all.js'))
        .scripts([
            func.path.assets('/vendor/braintree-web/client.js'),
            func.path.assets('/vendor/braintree-web/hosted-fields.js')
        ], func.path.public('/vendor/js/braintree-web/all.js'))
        .scripts([
            func.path.assets('/vendor/chart.js/dist/Chart.js'),
        ], func.path.public('/vendor/js/chart.js/all.js'))
        .scripts([
            func.path.assets('/vendor/jquery/dist/jquery.js'),
            func.path.assets('/vendor/jquery-ui/jquery-ui.js'),
            func.path.assets('/vendor/jquery.cookie/jquery.cookie.js'),
            func.path.assets('/vendor/bootstrap/dist/js/bootstrap.js'),
            func.path.assets('/vendor/bootstrap-toggle/js/bootstrap-toggle.js'),
            func.path.assets('/vendor/sticky-kit/jquery.sticky-kit.js'),
            func.path.assets('/vendor/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.js'),
            func.path.assets('/vendor/remarkable-bootstrap-notify/dist/bootstrap-notify.js'),
            func.path.assets('/vendor/underscore/underscore.js'),
            func.path.assets('/vendor/underscore.string/dist/underscore.string.js'),
            func.path.assets('/vendor/sprintf/src/sprintf.js'),
            func.path.assets('/vendor/jt.timepicker/jquery.timepicker.js'),
            func.path.assets('/vendor/jquery.business-hours/jquery.businessHours.js'),
            func.path.assets('/vendor/typeahead.js/dist/typeahead.bundle.js'),
            func.path.assets('/vendor/jquery.payment/lib/jquery.payment.js'),
            func.path.assets('/vendor/simplelightbox/dist/simple-lightbox.js'),
            func.path.assets('/vendor/jquery-mentiony/js/jquery.mentiony.js'),
            func.path.assets('/vendor/images-grid/src/images-grid.js'),
            func.path.assets('/vendor/Croppie/croppie.js')


        ], func.path.public('/js/vendor.js'))
        .appConfigJS(func.path.assets('/js/config/core/dev/app.js'),  func.path.assets('/js/config/core/build'))
        .scripts([
            func.path.assets('/js/config/core/build/app.js'),
            func.path.assets('/js/config/libraries/*.js'),
            func.path.assets('/js/libraries/*.js'),
            func.path.assets('/js/layouts/smart.js'),
        ], func.path.public('/js/app.js'))
        .appLESS(func.path.assets('/less/shares/**/*.less'), func.path.public('/css/shares'))
        .appLESS(func.path.assets('/less/widgets/**/*.less'), func.path.public('/css/widgets'))
        .appLESS(func.path.assets('/less/app/layouts/*.less'), func.path.public('/css/app/layouts'))
        .appLESS(func.path.assets('/less/app/modules/**/*.less'), func.path.public('/css/app/modules'))
        .appJS(func.path.assets('/js/shares/**/*.js'), func.path.public('/js/shares'))
        .appJS(func.path.assets('/js/widgets/**/*.js'), func.path.public('/js/widgets'))
        .appJS(func.path.assets('/js/app/layouts/**/*.js'), func.path.public('/js/app/layouts'))
        .appJS(func.path.assets('/js/app/modules/**/*.js'), func.path.public('/js/app/modules'))
        .version([

            func.path.public('/vendor/css/**/*.css'),
            func.path.public('/vendor/js/**/*.js'),

            func.path.public('/css/vendor.css'),
            func.path.public('/css/app.css'),
            func.path.public('/css/shares/**/*.css'),
            func.path.public('/css/widgets/**/*.css'),
            func.path.public('/css/app/layouts/*.css'),
            func.path.public('/css/app/modules/**/*.css'),

            func.path.public('/js/vendor.js'),
            func.path.public('/js/app.js'),
            func.path.public('/js/shares/**/*.js'),
            func.path.public('/js/widgets/**/*.js'),
            func.path.public('/js/app/layouts/*.js'),
            func.path.public('/js/app/modules/**/*.js'),
            func.path.public('/js/sockets/*.js'),

        ]);

    if(config['AWS_S3_SYNC_SCRIPT_ENABLE'] > 0) {
        mix.aws(func.path.public('/build/**'), 'build/');
    }

});