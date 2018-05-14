'use strict'
const utils = require('./utils')
const webpack = require('webpack')
const config = require('../config')
const merge = require('webpack-merge')
const path = require('path')
const baseWebpackConfig = require('./webpack.base.conf')
const CopyWebpackPlugin = require('copy-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const FriendlyErrorsPlugin = require('friendly-errors-webpack-plugin')
var BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const portfinder = require('portfinder')

// const HOST = process.env.HOST
// const PORT = process.env.PORT && Number(process.env.PORT)

const devWebpackConfig = merge(baseWebpackConfig, {
    module: {
        rules: utils.styleLoaders({sourceMap: config.dev.cssSourceMap, usePostCSS: true})
    },
    watch: true,
    // cheap-module-eval-source-map is faster for development
    devtool: config.dev.devtool,
    // devServer: {
    //     clientLogLevel: 'warning',
    //     historyApiFallback: {
    //         rewrites: [
    //             {from: /.*/, to: path.posix.join(config.dev.assetsPublicPath, 'index.html')},
    //         ],
    //     },
    //     hot: true,
    //     contentBase: false, // since we use CopyWebpackPlugin.
    //     compress: true,
    //     host: HOST || config.dev.host,
    //     port: PORT || config.dev.port,
    //     open: config.dev.autoOpenBrowser,
    //     overlay: config.dev.errorOverlay
    //         ? {warnings: false, errors: true}
    //         : false,
    //     publicPath: config.dev.assetsPublicPath,
    //     proxy: config.dev.proxyTable,
    //     quiet: true, // necessary for FriendlyErrorsPlugin
    //     watchOptions: {
    //         poll: config.dev.poll,
    //     }
    // },
    plugins: [
        new webpack.DefinePlugin({
            'process.env': require('../config/dev.env')
        }),
        // new webpack.HotModuleReplacementPlugin(),
        // new webpack.NamedModulesPlugin(), // HMR shows correct file names in console on update.
        // new webpack.NoEmitOnErrorsPlugin(),
        // https://github.com/ampedandwired/html-webpack-plugin
        // new HtmlWebpackPlugin({
        //     filename: 'index.html',
        //     template: 'index.html',
        //     inject: true
        // }),
        // copy custom static assets
        // new webpack.optimize.CommonsChunkPlugin({
        //     name: 'app',
        //     async: 'vendor-async',
        //     children: true,
        //     minChunks: 3
        // }),
        new CopyWebpackPlugin([
            {
                from: path.resolve(__dirname, '../static'),
                to: config.dev.assetsSubDirectory,
                ignore: ['.*']
            }
        ]),
        new BrowserSyncPlugin({
            // browse to http://localhost:3000/ during development,
            // ./public directory is being served
            host: 'localhost',
            port: 80,
            files: ['../../../application/modules/*.phtml'],
            proxy: 'http://localhost/'
            // server: { baseDir: ['dist'] }
        })
    ]
})

if (config.build.bundleAnalyzerReport) {
    const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin
    devWebpackConfig.plugins.push(new BundleAnalyzerPlugin())
}

module.exports = devWebpackConfig

// module.exports = new Promise((
// resolve, reject) => {
// //     portfinder.basePort = process.env.PORT || config.dev.port
// //     portfinder.getPort((err, port) => {
// //         if (err) {
// //             reject(err)
// //         } else {
// //             // publish the new Port, necessary for e2e tests
// //             process.env.PORT = port
// //             // add port to devServer config
// //             devWebpackConfig.devServer.port = port
// //
// //             // Add FriendlyErrorsPlugin
//             devWebpackConfig.plugins.push(new FriendlyErrorsPlugin({
//                 compilationSuccessInfo: {
//                     messages: [`Your application is running here`],
//                 },
//                 onErrors: config.dev.notifyOnErrors
//                     ? utils.createNotifierCallback()
//                     : undefined
//             }))
// //
//             resolve(devWebpackConfig)
//         // }
//     // })
// })
