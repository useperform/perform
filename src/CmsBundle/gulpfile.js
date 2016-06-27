var gulp = require('gulp');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var pump = require('pump');
var concat = require('gulp-concat');

gulp.task('sass', function (cb) {
  pump([
    gulp.src('Resources/scss/app.scss'),
    sass(),
    gulp.dest('Resources/public/css'),
  ], cb);
});

gulp.task('js', function (cb) {
  pump([
    gulp.src([
      'Resources/js/blocks.js',
      'Resources/js/*.block.js',
      'Resources/js/app.js',
    ]),
    concat('app.js'),
    // uglify(),
    gulp.dest('Resources/public/js')
  ], cb);
});

gulp.task('watch', function () {
  gulp.watch('Resources/scss/*.scss', ['sass']);
  gulp.watch('../Base/Resources/scss/*.scss', ['sass']);
  gulp.watch('Resources/js/*.js', ['js']);
});

gulp.task('build', ['sass']);
gulp.task('default', ['sass', 'js', 'watch']);
