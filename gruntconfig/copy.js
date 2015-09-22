'use strict';

var config = require('./config');

var copy = {
  build: {
    expand: true,
    cwd: config.src,
    dest: config.build + '/' + config.src,
    src: [
      '**/*',
      '!**/*.js',
      '!**/*.css',
      '!**/*.scss',
      '!**/*.orig'
    ]
  },
  dist: {
    expand: true,
    cwd: config.build + '/' + config.src,
    dest: config.dist,
    src: [
      '**/*',
      '!**/*.js',
      '!**/*.css'
    ]
  },
  leaflet: {
    expand: true,
    cwd: 'node_modules/leaflet/dist/images',
    dest: config.build + '/' + config.src + '/htdocs/images',
    src: [
      '**/*'
    ]
  },
  locationview: {
    expand: true,
    cwd: 'node_modules/hazdev-location-view/src/locationview',
    dest: config.build + '/' + config.src + '/htdocs',
    src: [
      'images/**'
    ]
  },
  test: {
    expand: true,
    cwd: config.test,
    dest: config.build + '/' + config.test,
    src: [
      '**/*',
      '!**/*.js',
      '!**/*.scss',
      '!**/*.orig'
    ]
  }
};

module.exports = copy;
