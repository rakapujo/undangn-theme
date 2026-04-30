module.exports = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	env: {
		browser: true,
		es2022: true,
	},
	parserOptions: {
		ecmaVersion: 2022,
		sourceType: 'module',
	},
	rules: {
		// Wajib const/let, no var (CLAUDE.md Bagian 4.3)
		'no-var': 'error',
		'prefer-const': 'error',
		// No jQuery (CLAUDE.md Bagian 8.3)
		'no-restricted-globals': [ 'error', '$', 'jQuery' ],
		// No emoji di kode (CLAUDE.md Bagian 8.3)
		'no-irregular-whitespace': 'error',
	},
	ignorePatterns: [ 'build/', 'node_modules/', 'vendor/' ],
};
