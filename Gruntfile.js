'use strict';

module.exports = function(grunt) {

  // time how long tasks take
  require('time-grunt')(grunt);

  // load all tasks
  require('load-grunt-tasks')(grunt);

  // Configure paths
  var config = {
    src: 'src_beforlogin',
    src_vendor: '<%= config.src %>/js/vendor/**/*.js',
    src_hbs: '<%= config.src %>/handlebars/**/*.hbs',
    assets_js: 'javascript/beforelogin',
    assets_css: 'stylesheet/beforelogin',
    assets_img: 'image/beforelogin'
  };

  grunt.initConfig({

    // Get package info
    pkg: grunt.file.readJSON('package.json'),

    // Project settings
    config: config,

    // Project banner
    tag: {
      banner: '/*!\n' +
              ' * <%= pkg.title %>\n' +
              ' * <%= pkg.url %>\n' +
              ' * @author <%= pkg.author %>\n' +
              ' */\n'
    },

    // Connect port/livereload
    connect: {
      options: {
        port: 9000,
        open: true,
        livereload: 35729,
        hostname: 'localhost'
      },
      livereload: {
        options: {
          middleware: function (connect) {
            return [
              // connect.static('.tmp'),
              // connect().use('/bower_components', connect.static('./bower_components')),
              // connect.static(config.dist)
            ];
          }
        }
      }
    },

    // Concatenate JavaScript files
    concat: {
      options: {
        separator: ';'
      },
      all: {
        src: [
          '<%= config.src %>/js/base/jquery-2.1.1.js',
          '<%= config.src %>/js/base/bootstrap.js',
          '<%= config.src %>/js/base/handlebars.runtime.min.js',
          '<%= config.src %>/js/base/template.js',
          '<%= config.src_vendor %>',
          '<%= config.src %>/js/jquery.pbAlert.js',
          '<%= config.src %>/js/script.js'
        ],
        dest: '<%= config.assets_js %>/script.min.js'
      }
    },

    // Uglify (minify) JavaScript files
    uglify: {
      options: {
        banner: '<%= tag.banner %>'
      },
      dist: {
        files: {
          '<%= config.assets_js %>/script.min.js': '<%= config.assets_js %>/script.min.js'
        }
      }
    },

    // Dev and distribution build for sass
    sass: {
      dev: {
        files: {
          'stylesheet/beforelogin/styles.css': '<%= config.src %>/scss/styles.scss'
        },
        options: {
          style: 'expanded'
        }
      },
      dist: {
        files: [{
          expand: true,
          cwd: '<%= config.src %>/scss',
          src: 'styles.scss',
          dest: '<%= config.assets_css %>',
          ext: '.css'
        }],
        options: {
          style: 'compressed'
        }
      }
    },

    // Compress Images
    imagemin: {
      dist: {
        files: [{
          expand: true,
          cwd: '<%= config.src %>/img',
          src: '{,*/}*.{gif,jpeg,jpg,png}',
          dest: '<%= config.assets_img %>/img'
        }]
      }
    },

    // SVG min
    svgmin: {
      options: {
        plugins: [
          { mergePaths: false },
          { cleanupIDs: false },
          { removeHiddenElems: false },
          { removeEmptyContainers: false },
          { removeUnknownsAndDefaults: false },
          { removeUselessStrokeAndFill: false }
        ]
      },
      dist: {
        files: [{
          expand: true,
          cwd: '<%= config.src %>/img/',
          src: '{,*/}*.svg',
          dest: '<%= config.assets_img %>/img/'
        }]
      }
    },

    /**
     * Precompile handlebars templates
     * https://github.com/gruntjs/grunt-contrib-handlebars
     */
    handlebars: {
      all: {
        files: [{
          src: '<%= config.src_hbs %>',
          dest: '<%= config.src %>/js/base/template.js'
        }],
        options: {
          namespace: 'template',
          processName: function(filename) {
            return filename
                    .replace(/^<%= config.src %>\/handlebars\//, '')
                    .replace(/\.hbs$/, '');
          }
        }
      }
    },

    // Run tasks in parallel to speed up build
    concurrent: {
      dev: [
        'sass:dev',
        'newer:imagemin:dist',
        'newer:svgmin:dist',
        'handlebars',
        'concat'
      ],
      dist: [
        'sass:dist',
        'imagemin:dist',
        'svgmin:dist',
        'handlebars',
        'concat'
      ]
    },

    // Watches for changes and runs tasks
    watch: {
      options: {
        livereload: true,
      },
      grunt: {
        files: ['Gruntfile.js'],
        tasks: []
      },
      sass: {
        files: ['<%= config.src %>/scss/{,*/}*.scss'],
        tasks: ['sass:dev']
      },
      js: {
        files: ['<%= config.src %>/js/**/*.js'],
        tasks: ['concat']
      },
      images: {
        files: ['<%= config.src %>/img/{,*/}*.{gif,jpeg,jpg,png}'],
        tasks: ['newer:imagemin:dist']
      },
      svgs: {
        files: ['<%= config.src %>/img/{,*/}*.svg'],
        tasks: ['newer:svgmin:dist']
      },
    }
 
  });

  // Default task
  grunt.registerTask('default', [
    // 'jshint',
    'concurrent:dev',
    'watch'
  ]);

  // Build task
  grunt.registerTask('build', [
    'concurrent:dist',
    'uglify'
  ]);

};
