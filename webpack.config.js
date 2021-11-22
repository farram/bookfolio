const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('calathea', './assets/main-calathea.js')
    .addEntry('dashboard', './assets/dashboard.js')
    .addEntry('front', './assets/front.js')
    .addEntry('admin-app', './assets/admin-app.js')

    /**
     * PORTFOLIO'S
     */
    .addEntry('tile-light', './assets/main-tile-light.js')
    .addEntry('tile-dark', './assets/main-tile-dark.js')
    .addEntry('tile-full-light', './assets/portfolio-tile-full-light.js')
    .addEntry('tile-full-dark', './assets/portfolio-tile-full-dark.js')
    .addEntry('tile-wide-dark', './assets/portfolio-wide-dark.js')
    .addEntry('tile-wide-light', './assets/portfolio-wide-light.js')
    .addEntry('alba-light', './assets/portfolio-alba-light.js')
    .addEntry('alba-dark', './assets/portfolio-alba-dark.js')
    .addEntry('illo-dark', './assets/portfolio-illo-dark.js')
    .addEntry('illo-light', './assets/portfolio-illo-light.js')
    .addEntry('mosaic-light', './assets/portfolio-mosaic-light.js')
    .addEntry('mosaic-dark', './assets/portfolio-mosaic-dark.js')
    .addEntry('folio-light', './assets/portfolio-folio-light.js')
    .addEntry('folio-dark', './assets/portfolio-folio-dark.js')
    .addEntry('reveal-light', './assets/portfolio-reveal-light.js')
    .addEntry('kool-light', './assets/portfolio-kool-light.js')
    .addEntry('kool-dark', './assets/portfolio-kool-dark.js')
    .addEntry('biggap-light', './assets/portfolio-biggap-light.js')
    .addEntry('biggap-dark', './assets/portfolio-biggap-dark.js')



    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    .enableVueLoader()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
    ;

module.exports = Encore.getWebpackConfig();
