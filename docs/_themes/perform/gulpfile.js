var gulp = require('gulp');
var sass = require('gulp-sass');
var run = require('gulp-run');
var browserSync = require('browser-sync').create();

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
    .pipe(gulp.dest('static/'))
  // need to pipe to the build output directory so browsersync works
    .pipe(gulp.dest('../../_build/html/_static/'))
    .pipe(browserSync.stream());
});

gulp.task('watch', function () {
  gulp.watch('scss/*.scss', ['sass']);
  gulp.watch('../../**/*.rst', ['html']);
  gulp.watch('*.html', ['html']);
});

gulp.task('html', function () {
  run('make html', {
    cwd: '../../'
  })
    .exec()
    .on('error', handleError);
});

gulp.task('browser-sync', function () {
  browserSync.init({
    open: false,
    server: {
      baseDir: "../../_build/html"
    }
  });
});

gulp.task('dev', ['watch', 'js', 'sass', 'browser-sync']);
gulp.task('build', ['sass', 'js', 'html']);
gulp.task('default', ['dev']);
