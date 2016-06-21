var gulp = require('gulp');
var sass = require('gulp-sass');

function handleError (err) {
  console.log(err);
  this.emit('end');
}

gulp.task('sass', function () {
  return gulp.src('Resources/scss/app.scss')
    .pipe(sass())
    .on('error', handleError)
    .pipe(gulp.dest('Resources/public/css'));
});

gulp.task('watch', function () {
  gulp.watch('Resources/scss/*.scss', ['sass']);
});

gulp.task('develop', ['watch', 'sass']);
gulp.task('build', ['sass']);
gulp.task('default', ['develop']);
