'use strict';

var concurrent = {
  scripts: [
    'jshint:scripts'
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
