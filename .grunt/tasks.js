module.exports = function(grunt) {

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// COMMANDS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	grunt.registerTask('default', 'Build assets for local', [
		'css',
		'js',
		'copy',
	]);

	grunt.registerTask('rebuild', 'Rebuild all assets from scratch', [
		'clean',
		'compass:clean',
		'default',
	]);

	grunt.registerTask('production', 'Build assets for production', [
		'js',
		'concat:css',
		'copy',
		'minify'
	]);

	// Flow
	////////////////////////////////////////////////////////////////////

	grunt.registerTask('minify', 'Minify assets', [
		'cssmin',
		'uglify',
	]);

	grunt.registerTask('images', 'Recompress images', [
		'svgmin',
		'tinypng',
	]);

	// By filetype
	////////////////////////////////////////////////////////////////////

	grunt.registerTask('md', 'Build contents', [
		'concat:md',
		'markdown',
		'prettify',
	]);

	grunt.registerTask('js', 'Build scripts', [
		'typescript',
		'jshint',
		'concat:js',
	]);

	grunt.registerTask('css', 'Build stylesheets', [
		'compass:compile',
		'csslint',
		'csscss',
		'autoprefixer',
		'concat:css',
	]);

}