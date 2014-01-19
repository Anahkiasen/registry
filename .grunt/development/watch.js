module.exports = {
	options: {
		livereload : true,
		interrupt  : true,
		poll       : true,
	},

	grunt: {
		files: 'Gruntfile.js',
		tasks: 'default',
	},
	img: {
		files: '<%= paths.original.img %>/**/*',
		tasks: 'copy',
	},
	js: {
		files: '<%= paths.original.js %>/**/*',
		tasks: 'js',
	},
	css: {
		files: '<%= paths.original.sass %>/**/*',
		tasks: 'css',
	},
	phpunit: {
		files: 'app/**/*.php',
		tasks: 'phpunit:core',
	},
};