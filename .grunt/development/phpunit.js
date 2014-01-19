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
			coverageText: 'app/tests/_coverage.txt',
			coverageHtml: 'app/tests/_coverage'
		}
	},

	all: {
	},
};