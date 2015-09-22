'use strict';

var config = require('./config');

var watch = {
  scripts: {
    files: [config.src + '/htdocs/**/*.js'],
    tasks: ['concurrent:scripts'],
    options: {
      livereload: true
    }
  },
  tests: {
    files: [
      config.src + '/htdocs/**/*.js',
      config.test + '**/*.js'
    ],
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
      livereload: true
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
      livereload: true
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
