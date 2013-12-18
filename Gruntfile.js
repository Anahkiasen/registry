module.exports = function(grunt) {

	// Load modules
	grunt.loadNpmTasks('grunt-bower-task');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-csslint');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-phpunit');

	// Project configuration.
	grunt.initConfig({

		//////////////////////////////////////////////////////////////////
		/////////////////////////////// PATHS ////////////////////////////
		//////////////////////////////////////////////////////////////////

		app        : 'public/app',
		builds     : 'public/builds',
		components : 'public/components',

		paths: {
			original: {
				css  : '<%= app %>/css',
				js   : '<%= app %>/js',
				sass : '<%= app %>/sass',
				img  : '<%= app %>/img',
			},
			compiled: {
				css : '<%= builds %>/css',
				js  : '<%= builds %>/js',
				img : '<%= builds %>/img',
			},
		},

		//////////////////////////////////////////////////////////////////
		/////////////////////////////// TASKS ////////////////////////////
		//////////////////////////////////////////////////////////////////

		// Development
		//////////////////////////////////////////////////////////////////

		phpunit: {
			options: {
				followOutput: true,
				stopOnFailure: grunt.option('sof') === true,
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
		},

		watch: {
			options: {
				livereload : true,
				interrupt  : true,
			},

			grunt: {
				files: 'Gruntfile.js',
				tasks: 'default',
			},
			scripts: {
				files: '<%= paths.original.js %>/**/*',
				tasks: 'js',
			},
			stylesheets: {
				files: '<%= paths.original.sass %>/**/*',
				tasks: 'css',
			},
			phpunit: {
				files: 'app/**/*.php',
				tasks: 'phpunit:core',
			},
		},

		clean: ['<%= builds %>'],

		// Assets
		//////////////////////////////////////////////////////////////////

		bower: {
			install: {
				options: {
					targetDir: '<%= components %>'
				}
			}
		},

		concat: {
			options: {
				sourcesContent: true
			},

			stylesheets: {
				files: {
					'<%= paths.compiled.css %>/styles.css': [
						'<%= components %>/normalize-css/normalize.css',
						'<%= components %>/icomoon/style.css',
						'<%= components %>/rainbow/themes/tomorrow-night.css',
						'<%= paths.original.css %>/*',
					],
				},
			},
			javascript: {
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
		},

		copy: {
			dist: {
				files: [
					{
						expand : true,
						src    : ['**'],
						cwd    : '<%= components %>/bootstrap/dist/fonts',
						dest   : '<%= builds %>/fonts/'
					},
					{
						expand : true,
						src    : ['**'],
						cwd    : '<%= paths.original.img %>',
						dest   : '<%= paths.compiled.img %>'
					}
				]
			}
		},

		cssmin: {
			minify: {
				expand : true,
				cwd    : '<%= paths.compiled.css %>',
				src    : '*.css',
				dest   : '<%= paths.compiled.css %>',
				ext    : '.min.css'
			}
		},

		uglify: {
			dest: {
				files: [{
					expand : true,
					cwd    : '<%= paths.compiled.js %>',
					src    : ['*.js'],
					dest   : '<%= paths.compiled.js %>',
					ext    : '.min.js',
				}],
			}
		},

		// Linting
		//////////////////////////////////////////////////////////////////

		csslint: {
			dist: {
				options: {
					'adjoining-classes'          : false,
					'box-model'                  : false,
					'box-sizing'                 : false,
					'compatible-vendor-prefixes' : false,
					'display-property-grouping'  : false,
					'duplicate-properties'       : false,
					'fallback-colors'            : false,
					'floats'                     : false,
					'font-sizes'                 : false,
					'gradients'                  : false,
					'important'                  : false,
					'known-properties'           : false,
					'qualified-headings'         : false,
					'star-property-hack'         : false,
					'text-indent'                : false,
					'unique-headings'            : false,
				},
				src: ['<%= paths.original.css %>/*']
			},
		},

		jshint: {
			options: {
				force   : true,

				boss    : true,
				browser : true,
				bitwise : true,
				curly   : true,
				devel   : true,
				eqeqeq  : true,
				eqnull  : true,
				immed   : true,
				indent  : 2,
				latedef : true,
				newcap  : true,
				noarg   : true,
				noempty : true,
				sub     : true,
				undef   : true,
				unused  : true,
				predef  : [
					"openModal",
				],
				globals : {
					$        : true,
					_        : true,
					Chart    : true,
					NodeList : true,
				}
			},

			all: ['<%= paths.original.js %>/*'],
		},

		// Preprocessors
		//////////////////////////////////////////////////////////////////

		compass: {
			options: {
				appDir             : "public/app/",
				cssDir             : "css",
				generatedImagesDir : "img/sprite/generated",
				imagesDir          : "img",
				outputStyle        : 'nested',
				noLineComments     : true,
				relativeAssets     : true,
				require            : ['susy', 'breakpoint', 'rgbapng'],
			},

			clean: {
				options: {
					clean: true,
				}
			},
			compile: {},
		}

	});

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// COMMANDS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	grunt.registerTask('default', 'Build assets for local', [
		'css', 'js',
		'copy',
	]);

	grunt.registerTask('production', 'Build assets for production', [
		'copy',
		'concat',
		'minify',
	]);

	grunt.registerTask('rebuild', 'Build assets from scratch', [
		'compass',
		'clean',
		'default',
	]);

	// Flow
	////////////////////////////////////////////////////////////////////

	grunt.registerTask('minify', 'Minify assets', [
		'cssmin',
		'uglify',
	]);

	// By filetype
	////////////////////////////////////////////////////////////////////

	grunt.registerTask('js', 'Build scripts', [
		'jshint',
		'concat:javascript',
	]);

	grunt.registerTask('css', 'Build stylesheets', [
		'compass:compile',
		'csslint',
		'concat:stylesheets'
	]);
};