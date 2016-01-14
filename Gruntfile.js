module.exports = function ( grunt ) {
	// Auto-load the needed grunt tasks
	// require('load-grunt-tasks')(grunt);
	require( 'load-grunt-tasks' )( grunt, { pattern: ['grunt-*'] } );

	var config = {
		tmpdir:                  '.tmp/',
		phpFileRegex:            '[^/]+\.php$',
		phpFileInSubfolderRegex: '.*?\.php$',
		pluginSlug:               'tabs-widget-for-page-builder',
	};

	// configuration
	grunt.initConfig( {
		pgk: grunt.file.readJSON( 'package.json' ),

		config: config,

		// https://npmjs.org/package/grunt-contrib-compass
		compass: {
			options: {
				sassDir:        'assets/sass',
				cssDir:         config.tmpdir,
				imagesDir:      'assets/images',
				outputStyle:    'compact',
				relativeAssets: true,
				noLineComments: true,
				quiet:          true,
				sourcemap:      false,
				importPath:     ['bower_components/bootstrap/scss']
			},
			dev: {
				options: {
					watch: true
				}
			},
			build: {
				options: {
					watch: false,
					force: true
				}
			}
		},

		// Apply several post-processors to your CSS using PostCSS.
		// https://github.com/nDmitry/grunt-postcss
		postcss: {
			options: {
				map:      true,
				processors: [
					require('autoprefixer')({browsers: ['last 2 versions', 'ie 10']}),
				]
			},
			build: {
				expand: true,
				cwd:    config.tmpdir,
				src:    '*.css',
				dest:   './assets/css/',
			},
			minified: {
				options: {
					processors: [
						require('cssnano')({
							discardComments: {removeAllButFirst: true}
						}),
					]
				},
				expand: true,
				cwd:    config.tmpdir,
				src:    '*.css',
				ext:    '.min.css',
				dest:   './assets/css/',
			},
		},

		// requireJS optimizer
		// https://github.com/gruntjs/grunt-contrib-requirejs
		requirejs: {
			build: {
				// Options: https://github.com/jrburke/r.js/blob/master/build/example.build.js
				options: {
					baseUrl:                 '',
					mainConfigFile:          'assets/js/main.js',
					optimize:                'uglify2',
					preserveLicenseComments: false,
					useStrict:               true,
					wrap:                    true,
					name:                    'bower_components/almond/almond',
					include:                 'assets/js/main',
					out:                     'assets/js/main.min.js'
				}
			}
		},

		// https://www.npmjs.com/package/grunt-wp-i18n
		makepot: {
			theme: {
				options: {
					domainPath:      'languages/',
					include:         [config.phpFileRegex, '^inc/'+config.phpFileInSubfolderRegex],
					mainFile:        'tabs-widget-for-page-builder.php',
					potComments:     'Copyright (C) {year} ProteusThemes \n# This file is distributed under the GPL 2.0.',
					potFilename:     config.pluginSlug + '.pot',
					potHeaders:      {
						poedit:                 true,
						'report-msgid-bugs-to': 'http://support.proteusthemes.com/',
					},
					type:            'wp-plugin',
					updateTimestamp: false,
					updatePoFiles:   true,
				}
			},
		},

		// https://www.npmjs.com/package/grunt-wp-i18n
		addtextdomain: {
			options: {
				updateDomains: true
			},
			target: {
				files: {
					src: [
						'*.php',
						'inc/**/*.php',
					]
				}
			}
		},

		// https://www.npmjs.com/package/grunt-po2mo
		po2mo: {
			files: {
				src:    'languages/*.po',
				expand: true,
			},
		},

	} );

	// build assets
	grunt.registerTask( 'build', [
		'compass:build',
		'postcss', // build and minified subtasks
		'requirejs:build',
	] );

	// update languages files
	grunt.registerTask( 'plugin_i18n', [
		'addtextdomain',
		'makepot:theme',
		'po2mo',
	] );
};