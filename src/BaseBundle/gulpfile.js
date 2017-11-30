var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('sass', function (cb) {
  return gulp.src('Resources/scss/app.scss')
    .pipe(sass({
      includePaths: ['node_modules']
    }))
    .on('error', sass.logError)
    .pipe(gulp.dest('Resources/public/css'));
});

gulp.task('js', function () {
  return gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/jquery.cookie/jquery.cookie.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/underscore/underscore-min.js',
    'node_modules/moment/min/moment.min.js',
    'node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    'node_modules/select2/dist/js/select2.min.js',
    'Resources/js/*.js',
    'Resources/js/*/*.js',
  ]).pipe(gulp.dest('Resources/public/js/'));
});

gulp.task('fonts', function () {
  return gulp.src([
    'node_modules/font-awesome/fonts/fontawesome-webfont.*',
    'node_modules/bootstrap-sass/assets/fonts/bootstrap/glyphicons-halflings-regular.*',
  ]).pipe(gulp.dest('Resources/public/fonts/'));
});

gulp.task('watch', function () {
  gulp.watch('Resources/scss/**/*.scss', ['sass']);
  gulp.watch('Resources/js/*.js', ['js']);
});

gulp.task('build', ['sass', 'js', 'fonts']);
gulp.task('dev', ['build', 'watch']);
