'use strict';

var config = require('./config');


var CWD = process.cwd(),
    EXPORTS = [],
    NODE_MODULES = CWD + '/node_modules';

EXPORTS = [
  NODE_MODULES + '/hazdev-webutils/src/util/Util.js:util/Util',
  NODE_MODULES + '/hazdev-webutils/src/util/Xhr.js:util/Xhr',
];


var browserify = {
  options: {
    browserifyOptions: {
      debug: true,
      paths: [
        CWD + '/' + config.src + '/htdocs/js',
        NODE_MODULES + '/hazdev-location-view/src',
        NODE_MODULES + '/hazdev-webutils/src'
      ]
    }
  },

  // source bundles
  index: {
    src: [config.src + '/htdocs/location.js'],
    dest: config.build + '/' + config.src + '/htdocs/location.js',
    options: {
      alias: EXPORTS
    }
  },

  // test bundle
  test: {
    src: config.test + '/test.js',
    dest: config.build + '/' + config.test + '/test.js'
  }
};


module.exports = browserify;
