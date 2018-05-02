const merge = require('webpack-merge')
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const common = require('./webpack.common.js')

module.exports = merge(common, {
  mode: 'production',
  module: {
     rules: [
       {
         test: /\.scss$/,
         use: ExtractTextPlugin.extract({
           fallback: 'style-loader',
           use: ['css-loader', 'sass-loader']
         })
       }
     ]
   },
   plugins: [
    new ExtractTextPlugin('style.css'),
    new UglifyJSPlugin({
      cache: true,
      parallel: true,
      sourceMap: true // set to true if you want JS source maps
    })
   ]
})
