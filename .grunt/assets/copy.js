module.exports = {
	dist: {
		files: [
			{
				expand : true,
				src    : ['**'],
				cwd    : '<%= components %>/icomoon/fonts',
				dest   : '<%= builds %>/css/fonts/'
			},
			{
				expand : true,
				src    : ['**'],
				cwd    : '<%= paths.original.img %>',
				dest   : '<%= paths.compiled.img %>'
			}
		]
	}
};