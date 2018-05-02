const path = require('path')
const CleanWebpackPlugin = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const {VueLoaderPlugin} = require('vue-loader')

module.exports = {
    entry: {
        app: './resources/scripts/app.js',
        proposta: './resources/scripts/proposta/proposta.js',
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
                }
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: [
                'vue-style-loader',
                'css-loader'
                ]
            }
        ]
    },
    plugins: [
        new CleanWebpackPlugin(['public/dist']),  // limpa o diretorio no build
        new HtmlWebpackPlugin({
            title: 'My killer app'
        }), // plugin para injetar o script diretamente no arquivo, só quando é spa
        new VueLoaderPlugin()
    ]
}
