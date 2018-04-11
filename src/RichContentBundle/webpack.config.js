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
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
          }
          // other vue-loader options go here
        }
      },
      {
        test: /\.scss$|\.css$/,
        exclude: /node-modules/,
        use: extractTextPlugin.extract({
          // use modules=1 for css modules scoped to each component
          use: ['css-loader', 'sass-loader'],
        }),
      }
    ],
  },
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.esm.js'
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  plugins: [
    new extractTextPlugin('editor.css'),
  ],
  devtool: '#eval-source-map'
};

if (process.env.NODE_ENV === 'production') {
  config.devtool = '#source-map';
  // http://vue-loader.vuejs.org/en/workflow/production.html
  config.plugins = (module.exports.plugins || []).concat([
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"'
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: true,
      compress: {
        warnings: false
      }
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: true
    })
  ]);
}

module.exports = config;
