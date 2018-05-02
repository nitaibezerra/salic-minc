const path = require('path')
const CleanWebpackPlugin = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
  entry: {
     app: './assets/scripts/app.js',
  },
  output: {
    filename: '[name]/[name].bundle.js',
    path: path.resolve(__dirname, 'public/dist')
  },
  module: {
     rules: [
       {
         test: /\.js$/,
         exclude: /(node_modules|bower_components)/,
         use: {
           loader: 'babel-loader',
         },
      }
     ]
  },
  plugins: [
     new CleanWebpackPlugin(['public/dist']),  // limpa o diretorio no build
     new HtmlWebpackPlugin({
       title: 'My killer app'
     }) // plugin para injetar o script diretamente no arquivo, só quando é spa
   ]
}
