var gulp = require('gulp');
var sass = require('gulp-sass');

function handleError (err) {
  console.log(err);
  this.emit('end');
}

gulp.task('js', function () {
  return gulp.src('node_modules/bootstrap/dist/js/bootstrap.min.js')
    .pipe(gulp.dest('static/'));
});

gulp.task('sass', function () {
  return gulp.src('scss/theme.scss')
    .pipe(sass())
    .on('error', handleError)
    .pipe(gulp.dest('static/'));
});

gulp.task('watch', function () {
  gulp.watch('scss/*.scss', ['sass']);
});

gulp.task('dev', ['watch', 'sass']);
gulp.task('build', ['sass', 'js']);
gulp.task('default', ['dev']);
