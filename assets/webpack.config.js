var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('../public/build/')
    .setPublicPath('/build')

    .addEntry('js/app', './js/app.js')
    .addEntry('js/statistics', './js/statistics.js')

    .addStyleEntry('css/app', './css/app.css')
    .addStyleEntry('css/themes/light-mode', './css/themes/light-mode.css')
    .addStyleEntry('css/themes/dark-mode', './css/themes/dark-mode.css')
    .addStyleEntry('css/export', './css/export.css')
    .addStyleEntry('css/flags', './css/flags.css')

    .addPlugin(new CopyWebpackPlugin([
        { from: './img', to: 'images', ignore: ['flags/**/*'] }
    ]))

    .configureUrlLoader({
        images: { limit: 4096 },
        fonts: { limit: 10240 }
    })

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .autoProvidejQuery()
    .disableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();

