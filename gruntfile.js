module.exports = function(grunt) {

  // Initializes the Grunt tasks with the following settings
  grunt.initConfig({

    // A list of files, which will be syntax-checked by JSHint
    jshint: {  },

    // Files to be concatenated … (source and destination files)
    concat: {
      options: {
        separator: ';',
        stripBanners: true
      },
      dist: {
        src: ['securewp/wp-content/themes/hotel-xenia/assets/js/map.js',
              'securewp/wp-content/themes/hotel-xenia/assets/js/owl.carousel.min.js',
              'securewp/wp-content/themes/hotel-xenia/assets/js/theme.js',
              'securewp/wp-content/themes/hotel-xenia/assets/js/main.js'],
        dest: 'securewp/wp-content/themes/hotel-xenia/assets/js/main.min.js'
      }
    },

    // … and minified (source and destination files)
    uglify: {
      my_target: {
        files: {
          'securewp/wp-content/themes/hotel-xenia/assets/js/main.min.js': ['securewp/wp-content/themes/hotel-xenia/assets/js/main.min.js']
        }
      }  
    },

    // Tasks being executed with 'grunt less'
    less: {
      production: {
        options: {
          paths: ['css'],
          yuicompress: true
        },
        files: {
          'securewp/wp-content/themes/hotel-xenia-child/css/main.min.css': 'wp-content/themes/elgi_blog/style/main.less'
        }
      }
    }

  });

  // Load the plugins that provide the tasks we specified in package.json.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');


  // This is the default task being executed if Grunt
  // is called without any further parameter.
  grunt.registerTask('default', ['concat', 'uglify', 'less']);

};  