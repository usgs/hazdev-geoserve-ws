'use strict';

var config = require('./config');


var jshint = {
  options: {
    jshintrc: '.jshintrc'
  },

  gruntfile: [
    'Gruntfile.js',
    'gruntconfig/**/*.js'
  ]
};


module.exports = jshint;
