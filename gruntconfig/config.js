'use strict';

var fs = require('fs'),
    ini = require('ini');

var configIni = ini.parse(fs.readFileSync('./src/conf/config.ini').toString());


var config = {
  ini: configIni,

  build: '.build',
  dist: 'dist',
  lib: 'lib',
  src: 'src'
};

module.exports = config;
