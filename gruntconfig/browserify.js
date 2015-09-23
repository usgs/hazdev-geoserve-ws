'use strict';

var config = require('./config');


var CWD = '.',
    EXPORTS = [],
    NODE_MODULES = CWD + '/node_modules';

EXPORTS = [
  CWD + '/src/htdocs/js/geoserve/NearbyCitiesView.js:geoserve/NearbyCitiesView',
  CWD + '/src/htdocs/js/geoserve/NeicResponseView.js:geoserve/NeicResponseView',

  NODE_MODULES + '/hazdev-webutils/src/mvc/Model.js:mvc/Model',

  NODE_MODULES + '/hazdev-webutils/src/util/Util.js:util/Util',
  NODE_MODULES + '/hazdev-webutils/src/util/Xhr.js:util/Xhr'
];


var browserify = {
  options: {
    browserifyOptions: {
      debug: true,
      paths: [
        CWD + '/' + config.src + '/htdocs/js',
        NODE_MODULES + '/hazdev-leaflet/src',
        NODE_MODULES + '/hazdev-location-view/src',
        NODE_MODULES + '/hazdev-webutils/src',
        NODE_MODULES + '/leaflet/dist'
      ]
    }
  },

  // source bundles
  index: {
    src: [config.src + '/htdocs/location.js'],
    dest: config.build + '/' + config.src + '/htdocs/location.js'
  },


  // the bundle used by tests
  bundle: {
    src: [],
    dest: config.build + '/' + config.test + '/bundle.js',
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
