const path = require('path')

const webpack = require('webpack')
const merge = require('webpack-merge')
const common = require('./webpack.common.js')

module.exports = merge(common, {
  mode: 'development',
   devServer: {
     hot: true
   },
   plugins: [
     new webpack.HotModuleReplacementPlugin()
 ],
  devtool: 'inline-source-map', // mostra o erro no arquivo e nao no bundle compilado
  module: {
    rules: [
      {
        test: /\.scss$/,
         use: [{
           loader: 'style-loader'
          },
          {
            loader: 'css-loader'
          },
          {
           loader: 'sass-loader',
           options: {
            includePaths: ["assets/style"]         
          }
        }]
      }
     ]
   }
})
