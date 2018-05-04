// This file builds all legacy javascript files for the base bundle.
// Over time this functionality will be merged into the perform.js webpack build.
var gulp = require('gulp');

gulp.task('build', function () {
  return gulp.src([
    'node_modules/jquery.cookie/jquery.cookie.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/underscore/underscore-min.js',
    'node_modules/select2/dist/js/select2.min.js',
    'js/*.js',
    'js/*/*.js',
  ]).pipe(gulp.dest('public/js/'));
});

gulp.task('watch', function () {
  gulp.watch([
    'js/*.js',
    'js/types/*.js',
  ], ['build']);
});

gulp.task('dev', ['build', 'watch']);
