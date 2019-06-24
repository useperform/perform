const { series, parallel, src, dest, watch } = require('gulp');
const { exec } = require('child_process');
const sass = require('gulp-sass');
const browserSync = require('browser-sync');

const themeDir = '_themes/perform';
const cssWatch = 'scss/*.scss';
const fieldTypeWatch = '../src/*/FieldType/*.php';
const htmlWatch = themeDir+'/*.html';
const rstWatch = '**/*.rst';

function js() {
  return src('node_modules/bootstrap/dist/js/bootstrap.min.js')
    .pipe(dest(themeDir+'/static/'));
}

function css() {
  return src('scss/theme.scss')
    .pipe(sass())
    .pipe(dest(themeDir+'/static/'))
  // need to pipe to the build output directory so browsersync notices the change
    .pipe(dest('_build/html/_static/'))
    .pipe(browserSync.stream());
}

function generate() {
  return exec('../bin/gendocs.php');
}

function sphinx() {
  return exec('make html');
}

function refresh_page(done) {
  browserSync.reload();
  done();
}

exports.default = exports.build = series(
  generate,
  parallel(js, css),
  sphinx
);

exports.watch = function() {
  watch(cssWatch, css);
  watch(fieldTypeWatch, generate);
  watch(rstWatch, sphinx);
  watch(htmlWatch, sphinx);
}

exports.dev = function() {
  browserSync.init({
    open: false,
    server: {
      baseDir: '_build/html'
    }
  });
  watch(cssWatch, css);
  watch(fieldTypeWatch, generate);
  watch(rstWatch, series(sphinx, refresh_page));
  watch(htmlWatch, series(sphinx, refresh_page));
};
