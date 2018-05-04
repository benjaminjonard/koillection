var cleanCss = require('gulp-clean-css');
var concat = require('gulp-concat');
var concatCss = require('gulp-concat-css');
var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var replace = require('gulp-replace');
var rev = require('gulp-rev');
var revdel = require('gulp-rev-delete-original');
var clean = require('gulp-clean');

var env = 'prod'; //process.env.GULP_ENV;

var paths = {
    js: [
        './js/lazy-loading.js',
        '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js',
        '../vendor/willdurand/js-translation-bundle/Resources/public/js/translator.min.js',
        './js/fos_js_routes.js',
        './js/translations/config.js',
        './js/translations/*/*.js',
        './node_modules/jquery/dist/jquery.min.js',
        './node_modules/lightbox2/dist/js/lightbox.min.js',
        './node_modules/d3/d3.min.js',
        './node_modules/cal-heatmap/src/cal-heatmap.js',
        './js/d3/calendar.js',
        './js/d3/radial-tree.js',
        './js/d3/bar-chart.js',
        './node_modules/croppie/croppie.min.js',
        './node_modules/sortablejs/Sortable.min.js',
        './node_modules/materialize-css/dist/js/materialize.min.js',
        './js/filters.js',
        './js/main.js',
        './js/settings.js',
        './js/item.js',
        './js/collection.js',
        './js/autocomplete.js'
    ],
    css: {
        app: [
            './node_modules/font-awesome/css/font-awesome.min.css',
            './node_modules/materialize-css/dist/css/materialize.min.css',
            './node_modules/lightbox2/dist/css/lightbox.min.css',
            './node_modules/croppie/croppie.css',
            './css/main.css'
        ],
        themes: [
            './css/themes/*'
        ],
        export: [
            './node_modules/font-awesome/css/font-awesome.min.css',
            './css/export.css'
        ],
        translation: [
            './css/translation.css'
        ],
    },
    fonts: [
        '../assets/node_modules/materialize-css/dist/fonts/roboto/*',
        '../assets/font/cooper-hewitt/*',
        '../assets/node_modules/font-awesome/fonts/*'
    ],
    images: [
        '../assets/img/**/*'
    ],
    manifest: [
        '../public/build/js/app.js',
        '../public/build/css/app.css',
        '../public/build/css/export.css',
        '../public/build/css/themes/aubergine.css',
        '../public/build/css/themes/sunset.css',
        '../public/build/css/themes/teal.css'
    ]
};

gulp.task('clean', function () {
    return gulp.src('../public/build', {read: false, allowEmpty: true})
        .pipe(clean({force: true}));
});

gulp.task('build', function () {
    //Fonts
    gulp.src(paths.fonts).pipe(gulp.dest('../public/build/fonts/'));

    //Images
    gulp.src(paths.images).pipe(gulp.dest('../public/build/images'));

    //CSS
    gulp.src(paths.css.app)
        .pipe(concatCss('app.css', {'rebaseUrls': false }))
        .pipe(replace("url(\"../fonts/roboto/", "url(\"../fonts/"))
        .pipe(replace("url('../fonts/fontawesome-", "url('../fonts/fontawesome-"))
        .pipe(gulpif(env === 'prod', cleanCss()))
        .pipe(gulp.dest('../public/build/css'));

    gulp.src(paths.css.translation)
        .pipe(gulpif(env === 'prod', cleanCss()))
        .pipe(gulp.dest('../public/build/css'));

    gulp.src(paths.css.export)
        .pipe(concatCss('export.css', {'rebaseUrls': false }))
        .pipe(replace("url('../fonts/fontawesome-", "url('../fonts/fontawesome-"))
        .pipe(gulpif(env === 'prod', cleanCss()))
        .pipe(gulp.dest('../public/build/css'));

    gulp.src(paths.css.themes)
        .pipe(gulpif(env === 'prod', cleanCss()))
        .pipe(gulp.dest('../public/build/css/themes'));

    //Service worker
    gulp.src('./js/sw.js')
        .pipe(concat('sw.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(gulp.dest('../public'));

    //JS
    return gulp.src(paths.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(gulp.dest('../public/build/js'));
});

gulp.task('revision', function() {
    return gulp.src(paths.manifest, {base: '../public/'})
        .pipe(rev())
        .pipe(revdel())
        .pipe(gulp.dest(function(file) {
            return file.base
        }))
        .pipe(rev.manifest('../public/build/manifest.json', {base: '../public/build/'}))
        .pipe(gulp.dest('../public/build/'));
});

gulp.task('default', gulp.series('clean', 'build', 'revision'));
gulp.task('delivery', gulp.series('clean', 'build', 'revision'));
