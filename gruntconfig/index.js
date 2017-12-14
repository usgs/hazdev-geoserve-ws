'use strict';

var gruntConfig = {
  config: require('./config'),

  clean: require('./clean'),
  connect: require('./connect'),
  copy: require('./copy'),
  jshint: require('./jshint'),
  postcss: require('./postcss'),
  watch: require('./watch'),

  tasks: [
    'grunt-connect-proxy',
    'grunt-connect-rewrite',
    'grunt-contrib-clean',
    'grunt-contrib-connect',
    'grunt-contrib-copy',
    'grunt-contrib-jshint',
    'grunt-contrib-watch',
    'grunt-postcss'
  ]
};


module.exports = gruntConfig;
