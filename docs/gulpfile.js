var gulp = require('gulp');
var sass = require('gulp-sass');
var run = require('gulp-run');
var browserSync = require('browser-sync').create();

function handleError (err) {
  console.log(err);
  this.emit('end');
}

var themeDir = '_themes/perform';

gulp.task('js', function () {
  return gulp.src('node_modules/bootstrap/dist/js/bootstrap.min.js')
    .pipe(gulp.dest(themeDir+'/static/'));
});

gulp.task('sass', function () {
  return gulp.src('scss/theme.scss')
    .pipe(sass())
    .on('error', handleError)
    .pipe(gulp.dest(themeDir+'/static/'))
  // need to pipe to the build output directory so browsersync works
    .pipe(gulp.dest('_build/html/_static/'))
    .pipe(browserSync.stream());
});

gulp.task('watch', function () {
  gulp.watch('scss/*.scss', ['sass']);
  gulp.watch('**/*.rst', ['html']);
  gulp.watch(themeDir+'/*.html', ['html']);
});

gulp.task('html', function () {
  run('make html', {
  })
    .exec()
    .on('error', handleError);
});

gulp.task('browser-sync', function () {
  browserSync.init({
    open: false,
    server: {
      baseDir: "_build/html"
    }
  });
});

gulp.task('dev', ['watch', 'js', 'sass', 'browser-sync']);
gulp.task('build', ['sass', 'js', 'html']);
gulp.task('default', ['dev']);
