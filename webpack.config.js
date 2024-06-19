const path = require( 'path' );

module.exports = {
	target: 'browserslist',
	context: __dirname,
	entry: {
		widget: path.resolve( __dirname, 'src', 'widget.js' ),
		'blog-posts': path.resolve( __dirname, 'src', 'blog-posts.js' ),
	},
	output: {
		path: path.resolve( __dirname, 'build' ),
		filename: '[name].js',
		publicPath: './',
	},
	externals: {
		jquery: 'jQuery',
	},
	mode: 'development',
	devtool: 'inline-source-map',
	performance: {
		hints: false,
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
				options: {
					presets: [ [ '@babel/preset-env' ] ],
				},
			},
		],
	},
};
