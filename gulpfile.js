var gulp = require('gulp');
var sass = require('gulp-sass');
var prefix = require('gulp-autoprefixer');
var minify = require('gulp-minify-css');
var rename = require('gulp-rename');
var util = require('gulp-util');
var size = require('gulp-size');
var uglify = require('gulp-uglifyjs');

gulp.task('styles', function () {
  return gulp.src([`${__dirname}/scss/*.scss`])
    .pipe(sass({
      outputStyle: 'compressed',
      precision: 10
    }))
    .pipe(prefix('last 2 versions', 'ie 8'))
    .pipe(minify({ keepSpecialComments: 1 }))
    .pipe(size({
      showFiles: true,
      gzip: true,
      title: 'Styles'
    }))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(`${__dirname}/css/`));
});

gulp.task('scripts', function() {

  return gulp.src([
      'js/*.js',
      '!js/*.min.js'
    ])
    .pipe(uglify())
    .pipe(size({
      gzip: true,
      title: 'Scripts'
    }))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(`${__dirname}/js`));

});

gulp.task('watch', function () {
  gulp.watch('scss/**/*.scss', gulp.series('styles'));
  gulp.watch('js/**/*.js', gulp.series('scripts'));
});

gulp.task('default', gulp.parallel(['styles', 'scripts']));
