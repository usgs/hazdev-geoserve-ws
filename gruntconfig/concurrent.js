'use strict';

var concurrent = {
  scripts: [
    'jshint:scripts',
    'jshint:tests',
    'browserify'
  ],
  build: [
    'compass:dev',
    'copy:build',
    'jshint:scripts',
    'browserify'
  ],
  dist: [
    'copy:dist',
    'cssmin',
    'uglify'
  ]
};

module.exports = concurrent;
