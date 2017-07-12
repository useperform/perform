var path = require('path');
var extractTextPlugin = require('extract-text-webpack-plugin');

var config = {
  entry: {
    'editor': './Resources/js/editor.js',
    'type': './Resources/js/type.js',
  },
  output: {
    path: path.join(__dirname, 'Resources/public'),
    filename: "[name].js",
    libraryTarget: "umd",
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
        options: {
          cacheDirectory: true,
          presets: [
            'es2015',
            'react'
          ],
        }
      },
      {
        test: /\.scss$/,
        exclude: /node-modules/,
        use: extractTextPlugin.extract({
          // use modules=1 for css modules scoped to each component
          use: ['css-loader?modules=1', 'sass-loader'],
        }),
      }
    ],
  },
  resolve: {
    extensions: ['.js', '.jsx', '.scss'],
    modules: [
      path.join(__dirname, 'node_modules'),
    ],
  },
  plugins: [
    new extractTextPlugin('editor.css'),
  ]
};

if (process.env.NODE_ENV === 'production') {
} else {
  config.devtool = 'cheap-eval-source-map';
}

module.exports = config;
