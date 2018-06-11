var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

// This is the webpack configuration for the whole application,
// pulling in resources from the various perform bundles as well as
// your own code.
// You are welcome to modify this configuration to suit your needs.
// https://useperform.com/docs/base-bundle/ui/assets.html

// asset-paths.js is generated every time the cache is cleared.
// Use this to get entrypoints and asset namespaces (resolve.alias).
var assetPaths = require('./asset-paths.js');

// babel options need to be passed to both the babel-loader and vue-loader.
// vue-loader v15 will remove this requirement, and will handle
// javascript like any other .js$ file.
var babelOptions = {
  presets: [
    // explicitly resolve babel presets to prevent it
    // looking for them in entrypoint directories
    [require.resolve('babel-preset-env'), {
      "targets": {
        "browsers": ['last 2 versions', '> 1%'],
        "uglify": true
      },
      "modules": false
    }],
  ],
  babelrc: false,
};

module.exports = {
  entry: assetPaths.entrypoints,
  output: {
    path: path.resolve(__dirname, 'public/'),
    filename: '[name].js'
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            js: {
              loader: 'babel-loader',
              options: babelOptions,
            }
          }
        }
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: babelOptions,
        }
      },
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          use: [
            'css-loader',
            {
              loader: 'sass-loader',
              options: {
                // sass loader options
                // compact output is often useful for scanning the output for errors
                outputStyle: 'compact',
              }
            }
          ]
        })
      },
      {
        test: /(!webfont)\.(png|jpg|gif|svg)$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]?[hash]'
        }
      },
      {
        test: /webfont\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/',
          }
        }]
      },
    ]
  },
  resolve: {
    // tell webpack to look for npm deps in this directory, not in the
    // entrypoint directories
    modules: [
      path.resolve(__dirname, 'node_modules')
    ],
    alias: Object.assign({
      'vue$': 'vue/dist/vue.esm.js',
    }, assetPaths.namespaces),
    extensions: ['*', '.js', '.vue', '.json']
  },
  resolveLoader: {
    // also tell webpack to look for loaders in this directory
    modules: [
      path.resolve(__dirname, 'node_modules')
    ],
  },
  performance: {
    hints: false
  },
  devtool: '#eval-source-map',
  plugins: [
    new ExtractTextPlugin({
      filename: "[name].css",
    })
  ]
}

if (process.env.NODE_ENV === 'production') {
  module.exports.devtool = '#source-map';
  // http://vue-loader.vuejs.org/en/workflow/production.html
  module.exports.plugins = (module.exports.plugins || []).concat([
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
