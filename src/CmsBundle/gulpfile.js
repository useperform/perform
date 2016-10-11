var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var pump = require('pump');
var concat = require('gulp-concat');
var jshint = require('gulp-jshint');

gulp.task('sass', function (cb) {
  pump([
    gulp.src('Resources/scss/app.scss'),
    sass(),
    gulp.dest('Resources/public/css'),
  ], cb);
});

gulp.task('js', function (cb) {
  delete require.cache[require.resolve('./gulp-sources')];
  var sources = require('./gulp-sources');
  console.log(sources);
  pump([
    gulp.src(sources),
    jshint(),
    jshint.reporter('default', { verbose: true }),
    jshint.reporter('fail'),
    concat('app.js'),
    // uglify(),
    gulp.dest('Resources/public/js')
  ], cb);
});

gulp.task('watch', function () {
  gulp.watch('Resources/scss/*.scss', ['sass']);
  gulp.watch('../BaseBundle/Resources/scss/*.scss', ['sass']);
  gulp.watch('Resources/js/*.js', ['js']);
  gulp.watch('gulp-sources.js', ['js']);
});

gulp.task('build', ['sass', 'js']);
gulp.task('default', ['sass', 'js', 'watch']);
