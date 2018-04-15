var gulp = require('gulp');

gulp.task('js', function () {
  return gulp.src([
    'node_modules/jquery.cookie/jquery.cookie.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/underscore/underscore-min.js',
    'node_modules/select2/dist/js/select2.min.js',
    'Resources/js/*.js',
    'Resources/js/*/*.js',
  ]).pipe(gulp.dest('Resources/public/js/'));
});

gulp.task('watch', function () {
  gulp.watch([
    'Resources/js/*.js',
    'Resources/js/types/*.js',
  ], ['js']);
});

gulp.task('build', ['js']);
gulp.task('dev', ['build', 'watch']);
