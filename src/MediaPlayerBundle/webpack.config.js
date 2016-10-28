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
        loaders: ['babel'],
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
