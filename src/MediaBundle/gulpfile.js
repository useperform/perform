var gulp = require('gulp');

gulp.task('js', function() {
  gulp.src([
    'node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
    'node_modules/blueimp-file-upload/js/jquery.iframe-transport.js',
    'Resources/js/*.js',
  ])
    .pipe(gulp.dest('Resources/public/js'));
});

gulp.task('watch', function () {
  gulp.watch('Resources/js/*.js', ['js']);
});

gulp.task('build', ['js']);
gulp.task('dev', ['js', 'watch']);
