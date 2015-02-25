'use strict';

var concurrent = {
  scripts: [
    'jshint:scripts',
    'copy:build'
  ],
  build: [
    'compass:dev',
    'copy:build',
    'jshint:scripts'
  ],
  dist: [
    'copy:dist',
    'cssmin',
    'uglify'
  ]
};

module.exports = concurrent;
