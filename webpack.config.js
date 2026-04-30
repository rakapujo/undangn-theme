/**
 * Webpack config — extend @wordpress/scripts default.
 *
 * Multi-entry untuk conditional bundle (CLAUDE.md Bagian 6.4):
 * - frontend          → core, di-load di semua page
 * - feature bundles   → ditambah saat fitur diimplementasi (gallery, countdown, dst)
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		frontend: path.resolve( __dirname, 'src/js/frontend.js' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
		filename: '[name].js',
	},
};
