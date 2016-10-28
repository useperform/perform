var path = require('path');

var config = {
  context: path.join(__dirname, 'Resources/js/'),
  entry: [
    './main.js',
  ],
  output: {
    path: path.join(__dirname, 'Resources/public'),
    filename: 'app.js',
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel',
        query: {
          presets: [
            'es2015'
            //for webpack 2 tree shaking
            // ['es2015', {modules: false}]
          ],
        }
      },
    ],
  },
  resolveLoader: {
    root: [
      path.join(__dirname, 'node_modules'),
    ],
  },
  // resolve: {
  //   root: [
  //     path.join(__dirname, 'node_modules'),
  //   ],
  // },
};
module.exports = config;
