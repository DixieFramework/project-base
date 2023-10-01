const path = require('path');
const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .autoProvidejQuery()

    .setOutputPath('./lib/Dixie/Bundle/WebBundle/src/Resources/public/')
    .setPublicPath('/bundles/talavweb/')
    .setManifestKeyPrefix('bundles/talavweb')

    .addAliases({
        umbrella_core: path.join(__dirname, '/lib/Dixie/Bundle/CoreBundle/src/Resources/public/assets/'),
        umbrella_admin: path.join(__dirname, '/lib/Dixie/Bundle/WebBundle/src/Resources/private/assets/'),
    })
    .addEntry('admin', './lib/Dixie/Bundle/WebBundle/src/Resources/private/assets/admin.js')
    .enableSassLoader()

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()

    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // add hash after file name
    .configureFilenames({
        js: '[name].js?[chunkhash]',
        css: '[name].css?[contenthash]',
    })
;

module.exports = Encore.getWebpackConfig();
