var gulp = require('gulp');
var sass = require('gulp-sass');
var webpack = require('webpack-stream');
var webpackConfig = require('./webpack.config.js');

function handleError (err) {
    console.log(err);
    this.emit('end');
}

gulp.task('sass', function () {
    return gulp.src('Resources/scss/player.scss')
        .pipe(sass())
        .on('error', handleError)
        .pipe(gulp.dest('Resources/public/css'))
});

gulp.task('webpack', function() {
    return gulp.src('src/entry.js')
        .pipe(webpack(webpackConfig))
        .pipe(gulp.dest('Resources/public/js'));
})

gulp.task('webpack-watch', function() {
    var config = webpackConfig;
    config.watch = true;

    return gulp.src('src/entry.js')
        .pipe(webpack(config))
        .pipe(gulp.dest('Resources/public/js'));
})

gulp.task('watch', function () {
    gulp.watch('Resources/scss/*.scss', ['sass']);
});

gulp.task('dev', ['watch', 'sass', 'webpack-watch']);
gulp.task('build', ['sass', 'webpack']);
gulp.task('default', ['dev']);
