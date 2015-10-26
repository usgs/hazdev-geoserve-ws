'use strict';

var concurrent = {
  scripts: [
    'jshint:scripts',
    'jshint:tests',
    'browserify'
  ],

  build: [
    'postcss:dev',
    'copy:build',
    'copy:leaflet',
    'copy:locationview',
    'jshint:scripts',
    'browserify'
  ],

  dist: [
    'copy:dist',
    'postcss:dist',
    'uglify'
  ]
};


module.exports = concurrent;
