'use strict';

var config = require('./config');

var cssmin = {
  options: {
    report: 'min',
    processImport: false,
    noRebase: true
  },
  dist: {
    expand: true,
    cwd: config.build + '/' + config.src,
    dest: config.dist,
    src: 'htdocs/index.css'
  }
};

module.exports = cssmin;
