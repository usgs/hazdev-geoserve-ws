'use strict';

var fs = require('fs'),
    ini = require('ini');

var configIni = ini.parse(fs.readFileSync('./src/conf/config.ini').toString());

var BASE_PORT = 9040;


var config = {
  ini: configIni,

  build: '.build',
  dist: 'dist',
  distPort: BASE_PORT + 2,
  etc: 'etc',
  example: 'example',
  examplePort: BASE_PORT + 3,
  lib: 'lib',
  liveReloadPort: BASE_PORT + 9,
  src: 'src',
  srcPort: BASE_PORT,
  templatePort: BASE_PORT + 8,
  test: 'test',
  testPort: BASE_PORT + 1
};


module.exports = config;
