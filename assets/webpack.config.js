var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('./assets/public/build/')
    .setPublicPath('/build')

    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/statistics', './assets/js/statistics/statistics.js')

    .addStyleEntry('css/app', './assets/css/app.css')
    .addStyleEntry('css/themes/aubergine', './assets/css/themes/aubergine.css')
    .addStyleEntry('css/themes/sunset', './assets/css/themes/sunset.css')
    .addStyleEntry('css/themes/teal', './assets/css/themes/teal.css')
    .addStyleEntry('css/export', './assets/css/export.css')
    .addStyleEntry('css/translation', './assets/css/translation.css')

    .addPlugin(new CopyWebpackPlugin([
        { from: './img', to: 'images' }
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
