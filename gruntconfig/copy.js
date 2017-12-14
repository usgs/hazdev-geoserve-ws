'use strict';

var config = require('./config');


var copy = {
  build: {
    files: [
      {
        expand: true,
        cwd: config.src,
        dest: config.build + '/' + config.src,
        src: [
          '**/*',
          '!**/*.scss',
          '!**/*.orig'
        ]
      }
    ]
  },

  dist: {
    expand: true,
    cwd: config.build + '/' + config.src,
    dest: config.dist,
    src: [
      '**/*',
      '!**/*.css'
    ]
  }
};


module.exports = copy;
