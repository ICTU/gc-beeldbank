'use strict';

const del = require('del'),
  gulp = require('gulp'),
  sass = require('gulp-dart-sass'),
  autoprefixer = require('gulp-autoprefixer'),
  browserSync = require('browser-sync').create(),
  notify = require('gulp-notify'),
  sourcemaps = require('gulp-sourcemaps'),
  svgmin = require('gulp-svgmin'),
  iconfont = require('gulp-iconfont'),
  consolidate = require('gulp-consolidate'),
  minify = require("gulp-minify"),
  plumber = require("gulp-plumber"),
  concat = require('gulp-concat-util');


const r = Math.random().toString(36).substring(7);
const fontName = 'ho-iconfont-' + r;


/*  IconFont
 *  Iconfont only needs to be created for the base theme
 */

function makeFont(done) {
  del(['fonts/iconfont/**'], {
    'force': true
  });

  var iconStream = gulp.src('img/icons/*.svg')
    .pipe(svgmin())
    .pipe(iconfont({
      fontName: fontName,
      fontHeight: 1001,
      normalize: true
    }));

  const Glyphs = function (cb) {
    iconStream.on('glyphs', function (glyphs, options) {
      gulp.src('scss/base/_icons.scss')
        .pipe(consolidate('lodash', {
          glyphs: glyphs,
          fontName: fontName
        }))
        .pipe(gulp.dest('scss/abstracts'))
    });

    cb();
  };

  const handleFont = function (cb) {
    iconStream
      .pipe(gulp.dest('fonts/iconfont/'))
    cb();
  };

  iconStream.on('finish', function () {
    done();
  });

  return gulp.parallel(Glyphs, handleFont)();
}


function js(done) {
  del(['dist/js/*.js'], {force: true});

  gulp.src('js/components/*.js')
    .pipe(concat('main.js'))
    .pipe(concat.header('(function ($, document, window) { $(document).ready(function() {'))
    .pipe(concat.footer('}); })(jQuery, document, window);'))
    .pipe(plumber())
    .pipe(minify({
      noSource: true,
      ext: {
        min: '-min.js'
      },
    }))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('dist/js'))
    .pipe(notify({message: 'Theme JS Task complete'}));

  done();
}


function styles(done) {

  gulp
    .src('scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer('last 2 version'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('dist/css'))
    .pipe(notify({message: 'Theme Styles task complete'}))
    .pipe(browserSync.stream());

  done();
}


function prod(done) {

  gulp
    .src('scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer('last 2 version'))
    .pipe(gulp.dest('dist/css'))
    .pipe(notify({message: 'Prod CSS task complete'}))
  done();
}


// Watch files
function watch() {
  browserSync.init({
    proxy: 'gcbeeldbank.localhost'
  });

  gulp.watch('scss/**/*.scss', styles);
  gulp.watch('js/components/*.js', js);

  gulp.watch('img/icons/*.svg', gulp.series(makeFont, styles));

  //gulp.watch('styleguide/**/*.scss', styleGuide);
  //gulp.watch('styleguide/**/*.html', styleGuide);
}


exports.iconfont = gulp.series(makeFont, prod);
exports.prod = prod;
exports.styles = styles;
exports.js = js;
exports.default = watch;
//exports.styleguide = styleGuide;
