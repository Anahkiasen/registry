module.exports = {
	css: {
		files: {
			'<%= paths.compiled.css %>/styles.css': [
				'<%= components %>/normalize-css/normalize.css',
				'<%= components %>/icomoon/style.css',
				'<%= components %>/rainbow/themes/tomorrow-night.css',
				'<%= paths.original.css %>/*',
			],
		},
	},
	js: {
		files: {
			'<%= paths.compiled.js %>/home.js': [
				'<%= components %>/lodash/dist/lodash.underscore.min.js',
				'<%= paths.original.js %>/scripts.js',
			],
			'<%= paths.compiled.js %>/chart.js': [
				'<%= components %>/nnnick-chartjs/Chart.min.js',
				'<%= paths.original.js %>/chart.js',
			],
			'<%= paths.compiled.js %>/scripts.js': [
				'<%= components %>/rainbow/js/rainbow.min.js',
				'<%= components %>/rainbow/js/language/generic.js',
				'<%= components %>/rainbow/js/language/javascript.js',
				'<%= components %>/rainbow/js/language/php.js',
			],
		},
	}
};