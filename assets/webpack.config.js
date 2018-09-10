var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('../public/build/')
    .setPublicPath('/build')

    .addEntry('js/app', './js/app.js')

    .addStyleEntry('css/app', './css/app.css')
    .addStyleEntry('css/themes/aubergine', './css/themes/aubergine.css')
    .addStyleEntry('css/themes/sunset', './css/themes/sunset.css')
    .addStyleEntry('css/themes/teal', './css/themes/teal.css')
    .addStyleEntry('css/export', './css/export.css')
    .addStyleEntry('css/translation', './css/translation.css')

    .addPlugin(new CopyWebpackPlugin([{ from: './img', to: 'images' }]))

    /*.configureUrlLoader({
        images: { limit: 4096 }
    })*/

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
