const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        index: path.resolve(process.cwd(), 'ui/index.js'),
    },
    output: {
        ...defaultConfig.output,
        path: path.resolve(process.cwd(), 'build'),
    },
    resolve: {
        ...defaultConfig.resolve,
        modules: [
            path.resolve(process.cwd(), 'ui'),
            'node_modules'
        ]
    }
};