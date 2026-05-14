/**
 * @module Buildpack/WebpackTools
 */
const loaderUtils = require('loader-utils');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

/**
 * Replacement for the function `css-loader` uses to build classnames.
 * Without this, our `*.module.css` files yield very long classnames.
 *
 * @param {*} loaderContext
 * @param {*} localIdentName
 * @param {*} localName
 * @param {*} options
 * @returns {String} Transformed local identity name, aka classname
 */
function getLocalIdent(loaderContext, localIdentName, localName, options) {
    if (!options.context) {
        // eslint-disable-next-line no-param-reassign
        options.context = loaderContext.rootContext;
    }

    const request = path
        .relative(options.context, loaderContext.resourcePath)
        .replace(/\\/g, '/');

    // eslint-disable-next-line no-param-reassign
    options.content = `${options.hashPrefix + request}+${localName}`;

    // eslint-disable-next-line no-param-reassign
    localIdentName = localIdentName.replace(/\[local\]/gi, localName);

    const hash = loaderUtils.interpolateName(
        loaderContext,
        localIdentName,
        options
    );

    return hash
        .replace('.module', '')
        .replace(new RegExp('[^a-zA-Z0-9\\-_\u00A0-\uFFFF]', 'g'), '-')
        .replace(/^((-?[0-9])|--)/, '_$1');
}

/**
 * Create a Webpack
 * [module rules object](https://webpack.js.org/configuration/module/#rule) for
 * processing all the filetypes that the project will contain.
 *
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns {Object[]} Array of Webpack rules.
 */
async function getModuleRules(helper) {
    return Promise.all([
        getModuleRules.graphql(helper),
        getModuleRules.js(helper),
        getModuleRules.css(helper),
        getModuleRules.files(helper),
        getModuleRules.scss(helper),
        getModuleRules.font(helper)
    ]);
}

/**
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns Rule object for Webpack `module` configuration which parses
 *   `.graphql` files
 */
getModuleRules.graphql = async ({ paths, hasFlag, extendPaths = [] }) => ({
    test: /\.graphql$/,
    include: [paths.src, ...hasFlag('graphqlQueries'), ...extendPaths],
    use: [
        {
            loader: 'graphql-tag/loader'
        }
    ]
});

/**
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns Rule object for Webpack `module` configuration which parses
 *   JavaScript files
 */
getModuleRules.js = async ({
    mode,
    paths,
    hasFlag,
    babelRootMode,
    transformRequests,
    extendPaths = []
}) => {
    const overrides = Object.entries(transformRequests.babel).map(
        ([plugin, requestsByFile]) => ({
            test: Object.keys(requestsByFile),
            plugins: [[plugin, { requestsByFile }]]
        })
    );

    const astLoaders = [
        {
            // Use custom loader to enable warning reporting from Babel plugins
            loader:
                '@magento/pwa-buildpack/lib/WebpackTools/loaders/buildbus-babel-loader.js',
            options: {
                sourceMaps: mode === 'development' && 'inline',
                envName: mode,
                root: paths.root,
                rootMode: babelRootMode,
                overrides
            }
        }
    ];

    const sourceLoaders = Object.entries(transformRequests.source).map(
        ([loader, requestsByFile]) => {
            return {
                test: Object.keys(requestsByFile),
                use: [
                    info => ({
                        loader,
                        options: requestsByFile[info.realResource].map(
                            req => req.options
                        )
                    })
                ]
            };
        }
    );

    return {
        test: /\.(mjs|js|jsx)$/,
        include: [paths.src, ...hasFlag('esModules'), ...extendPaths],
        sideEffects: false,
        rules: [...astLoaders, ...sourceLoaders]
    };
};

/**
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns Rule object for Webpack `module` configuration which parses
 *   CSS files
 */
getModuleRules.css = async ({ hasFlag, mode, extendPaths = [] }) => ({
    test: /\.css$/,
    oneOf: [
        {
            test: [/\.tailwind\.css$/],
            use:
                mode === 'development'
                    ? [
                          {
                              loader: 'style-loader',
                              options: {
                                  injectType: 'styleTag'
                              }
                          },
                          {
                              loader: 'css-loader',
                              options: {
                                  importLoaders: 1,
                                  modules: false,
                                  sourceMap: true
                              }
                          },
                          'postcss-loader'
                      ]
                    : [
                          {
                              loader: MiniCssExtractPlugin.loader
                          },
                          {
                              loader: 'css-loader',
                              options: {
                                  importLoaders: 1,
                                  modules: false,
                                  sourceMap: false
                              }
                          },
                          'postcss-loader'
                      ]
        },
        {
            test: [/\.module\.css$/, ...hasFlag('cssModules'), ...extendPaths],
            use:
                mode === 'development'
                    ? [
                          {
                              loader: 'style-loader',
                              options: {
                                  injectType: 'styleTag'
                              }
                          },
                          {
                              loader: 'css-loader',
                              options: {
                                  importLoaders: 1,
                                  modules: {
                                      getLocalIdent,
                                      localIdentName: `[name]-[local]-[hash:base64:3]`
                                  },
                                  sourceMap: true
                              }
                          },
                          'postcss-loader'
                      ]
                    : [
                          {
                              loader: MiniCssExtractPlugin.loader
                          },
                          {
                              loader: 'css-loader',
                              options: {
                                  importLoaders: 1,
                                  modules: {
                                      getLocalIdent,
                                      localIdentName: `[name]-[local]-[hash:base64:3]`
                                  },
                                  sourceMap: false
                              }
                          },
                          'postcss-loader'
                      ]
        },
        {
            use:
                mode === 'development'
                    ? [
                          {
                              loader: 'style-loader',
                              options: {
                                  injectType: 'styleTag'
                              }
                          },
                          {
                              loader: 'css-loader',
                              options: {
                                  importLoaders: 1,
                                  modules: false,
                                  sourceMap: true
                              }
                          },
                          'postcss-loader'
                      ]
                    : [
                          {
                              loader: MiniCssExtractPlugin.loader
                          },
                          {
                              loader: 'css-loader',
                              options: {
                                  importLoaders: 1,
                                  modules: false,
                                  sourceMap: false
                              }
                          },
                          'postcss-loader'
                      ]
        }
    ]
});

/**
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns Rule object for Webpack `module` configuration which parses
 *   and inlines binary files below a certain size
 */
getModuleRules.files = async () => ({
    test: /\.(gif|jpg|png|svg|webp)$/,
    use: [
        {
            loader: 'file-loader',
            options: {
                name: '[name]-[hash:base58:3].[ext]'
            }
        }
    ]
});

/**
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns Rule object for Webpack `module` configuration which parses
 *   SCSS files
 */
getModuleRules.scss = async () => ({
    test: /\.s[ac]ss$/i,
    use: [
        'style-loader',
        'css-loader',
        {
            loader: 'sass-loader'
        }
    ]
});

/**
 * @param {Buildpack/WebpackTools~WebpackConfigHelper} helper
 * @returns Rule object for Webpack `module` configuration which parses
 *   Font files
 */
getModuleRules.font = async () => ({
    test: /\.(woff|woff2|ttf|otf)$/,
    loader: 'file-loader',
    include: [/fonts/],
    options: {
        name: '[name].[ext]'
    }
});

module.exports = getModuleRules;
