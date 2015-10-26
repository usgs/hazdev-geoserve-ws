'use strict';

var autoprefixer = require('autoprefixer'),
    cssnano = require('cssnano'),
    precss = require('precss'),
    postcssImport = require('postcss-import');

var config = require('./config');
var CWD = process.cwd();


var postcss = {
  dev: {
    options: {
      map: true,
      processors: [
        postcssImport({
          path: [
            CWD + '/' + config.src + '/htdocs/css',
            CWD + '/node_modules/hazdev-leaflet/src',
            CWD + '/node_modules/hazdev-location-view/src',
            CWD + '/node_modules/hazdev-template/src/htdocs',
            CWD + '/node_modules/hazdev-webutils/src',
            CWD + '/node_modules/leaflet/dist'
          ]
        }),
        precss(),
        autoprefixer({'browsers': 'last 2 versions'}), // vendor prefix as needed
      ]
    },
    expand: true,
    cwd: config.src + '/htdocs',
    src: '*.scss',
    dest: config.build + '/' + config.src + '/htdocs',
    ext: '.css',
    extDot: 'last'
  },

  dist: {
    options: {
      processors: [
        cssnano({zindex: false}) // minify
      ]
    },
    expand: true,
    cwd: config.build + '/' + config.src + '/htdocs',
    src: '*.css',
    dest: config.dist + '/htdocs'
  }
};


module.exports = postcss;
