module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		autoprefixer: {
			options: {
				browsers: ['last 2 versions', 'Firefox ESR', 'Opera 12.1', 'ie 9', 'android 2.3', 'android 4']
			},
			single_file: {
				src: 'style.css',
				dest: 'style.css'
			}
		},

		makepot: {
			plugin: {
				options: {
					type: 'wp-plugin',
					domainPath: 'languages'
				}
			}
		}

	});


	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-wp-i18n');


	grunt.registerTask('default', ['autoprefixer', 'makepot']);

};
