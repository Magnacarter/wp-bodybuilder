"use strict";

var gulp         = require( 'gulp' ),
    maps         = require( 'gulp-sourcemaps' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    concat       = require( 'gulp-concat' ),
    uglify       = require( 'gulp-uglify' ),
    rename       = require( 'gulp-rename' ),
    sass         = require( 'gulp-sass' ),
    cleanCSS     = require( 'gulp-clean-css' ),
    del          = require( 'del' ),
    browserSync  = require( 'browser-sync' );

var path = {
  JS_PUB          : '../js/pub/js',
  JS_PUB_FILES    : [ '../pub/js/pub-script.js' ],
  SCSS_PUB        : '../pub/scss/main.scss',
  CSS_PUB         : '../pub/css',
  CSS_PUB_FILES   : [ '../pub/css/unsemantic-grid-responsive.css', '../pub/css/pub.css' ],
  SCSS_ADMIN      : '../admin/scss/main.scss',
  CSS_ADMIN       : '../admin/css',
  JS_ADMIN        : '../admin/js',
  JS_ADMIN_FILES  : [ '../admin/js/admin-script.js', '../jquery.rateyo.min.js', '../select2.min.js' ],
  CSS_ADMIN_FILES : [ '../admin/css/jquery.rateyo.min.css', '../admin/css/select2.min.css', '../admin/css/unsemantic-grid-responsive.css', '../admin/css/admin.css' ],
}

/**
 * Admin scripts
 */
gulp.task( 'concatAdminScripts', function() {
  return gulp.src( path.JS_ADMIN_FILES )
    .pipe( maps.init() )
    .pipe( concat( 'admin-app.js' ) )
    .pipe( maps.write( './' ) )
    .pipe( gulp.dest( path.JS_ADMIN ) );
});

/**
 * Public scripts
 */
gulp.task( 'concatScripts', function() {
  return gulp.src( path.JS_PUB_FILES )
    .pipe( maps.init() )
    .pipe( concat( 'app.js' ) )
    .pipe( maps.write( './' ) )
    .pipe( gulp.dest( path.JS_PUB ) );
});

/**
 * Admin sass
 */
gulp.task( 'compileAdminSass', function() {
  return gulp.src( path.SCSS_ADMIN )
    .pipe( maps.init() )
    .pipe( sass().on( 'error', sass.logError ) )
    .pipe( autoprefixer( { browsers: ["> 0%"] } ) )
    .pipe( rename( 'admin.css' ) )
    .pipe( maps.write() )
    .pipe( gulp.dest( path.CSS_ADMIN ) )
});

/**
 * Admin css
 */
gulp.task( 'concatAdminCSS', [ 'compileAdminSass' ], function() {
  return gulp.src( path.CSS_ADMIN_FILES )
    .pipe( cleanCSS( { compatibility: 'ie8' } ) )
    .pipe( concat( 'admin-styles.css' ) )
    .pipe( gulp.dest( path.CSS_ADMIN ) );
});

/**
 * Public sass
 */
gulp.task( 'compileSass', function() {
  return gulp.src( path.SCSS_PUB )
    .pipe( maps.init() )
    .pipe( sass().on( 'error', sass.logError ) )
    .pipe( autoprefixer( { browsers: ["> 0%"] } ) )
    .pipe( rename( 'pub.css' ) )
    .pipe( maps.write() )
    .pipe( gulp.dest( path.CSS_PUB ) )
});

/**
 * Public css
 */
gulp.task( 'concatCSS', [ 'compileSass' ], function() {
  return gulp.src( path.CSS_PUB_FILES )
    .pipe( cleanCSS( { compatibility: 'ie8' } ) )
    .pipe( concat( 'pub-styles.css' ) )
    .pipe( gulp.dest( path.CSS_PUB ) );
});

gulp.task( 'cssWatch', [ 'concatCSS' ], browserSync.reload );

gulp.task( 'jsWatch', [ 'concatScripts' ], browserSync.reload );

gulp.task( 'cssAdminWatch', [ 'concatAdminCSS' ], browserSync.reload );

gulp.task( 'jsAdminWatch', [ 'concatAdminScripts' ], browserSync.reload );

gulp.task( 'watchFiles', function() {
  browserSync({
    proxy: {
      target: 'http://testsite.dev/'
    }
  });
  gulp.watch([ '../pub/scss/**/*.scss' ], [ 'cssWatch' ]);
  gulp.watch( '../pub/js/pub-script.js', [ 'jsWatch' ]);
  gulp.watch([ '../admin/scss/**/*.scss' ], [ 'cssAdminWatch' ]);
  gulp.watch( '../admin/js/admin-script.js', [ 'jsAdminWatch' ]);
});

gulp.task( 'serve', [ 'watchFiles' ] );

gulp.task( 'default', [ 'serve' ], function() {
  console.log( 'Welcome to the WP Bodybuilder Gulp task runner!' );
});