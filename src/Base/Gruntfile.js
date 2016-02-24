var webpack = require('webpack');

module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      dist: {
        files: {
          'Resources/public/css/app.css' : 'Resources/scss/app.scss'
        }
      }
    },
    watch: {
      css: {
        files: 'Resources/scss/*.scss',
        tasks: ['sass']
      }
    },
    symlink: {
      explicit: {
        src: 'Resources/vendor/font-awesome/fonts',
        dest: 'Resources/public/fa-fonts'
      },
    },
    webpack: {
      someName: {
        entry: "./Resources/js/app.js",
        output: {
          path: "./Resources/public/js",
          filename: "app.js"
        },
        resolve: {
          alias: {
            jquery: __dirname+"/Resources/vendor/jquery/dist/jquery.js"
          }
        },
        plugins: [
          new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
          })
        ]
      }
    }
  });
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-symlink');
  grunt.loadNpmTasks('grunt-webpack');
  grunt.registerTask('default',['watch']);
  grunt.registerTask('build',['symlink', 'sass']);
}
