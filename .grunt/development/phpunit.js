module.exports = {
	options: {
		followOutput: true,
	},

	core: {
		options: {
			excludeGroup: 'Routes',
		}
	},

	coverage: {
		options: {
			excludeGroup: 'Routes',
			coverageText: '<%= tests %>/.coverage.txt',
			coverageHtml: '<%= tests %>/.coverage'
		}
	},

	all: {
	},
};