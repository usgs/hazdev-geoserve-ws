'use strict';

var config = require('./config');


var watch = {
  scripts: {
    files: [config.src + '/htdocs/**/*.js'],
    tasks: ['concurrent:scripts'],
    options: {
      livereload: config.liveReloadPort
    }
  },
  tests: {
    files: [config.test + '/**/*.js'],
    tasks: ['concurrent:scripts']
  },
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
      '!' + config.src + '/**/*.js'
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
