var gulp = require('gulp');
var sass = require('gulp-sass');
var pump = require('pump');

gulp.task('sass', function (cb) {
  pump([
    gulp.src('Resources/scss/app.scss'),
    sass(),
    gulp.dest('Resources/public/css'),
  ], cb);
});

gulp.task('watch', function () {
  gulp.watch('Resources/scss/*.scss', ['sass']);
});

gulp.task('build', ['sass']);
gulp.task('default', ['sass', 'watch']);
