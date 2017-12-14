'use strict';

var config = require('./config');


var watch = {
  scss: {
    files: [
      config.src + '/htdocs/**/*.css',
      config.src + '/htdocs/**/*.scss'
    ],
    tasks: [
      'postcss:dev'
    ],
    options: {
      livereload: config.liveReloadPort
    }
  },
  livereload: {
    files: [
      config.src + '/**/*',
      '!' + config.src + '/**/*.css',
    ],
    tasks: ['copy:build'],
    options: {
      livereload: config.liveReloadPort
    }
  },
  gruntfile: {
    files: [
      'Gruntfile.js',
      'gruntconfig/**/*.js'
    ],
    tasks: ['jshint:gruntfile']
  }
};


module.exports = watch;
