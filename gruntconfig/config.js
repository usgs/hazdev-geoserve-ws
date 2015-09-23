'use strict';

var fs = require('fs'),
    ini = require('ini');

var configIni = ini.parse(fs.readFileSync('./src/conf/config.ini').toString());


var config = {
  ini: configIni,

  build: '.build',
  dist: 'dist',
  distPort: 8102,
  etc: 'etc',
  example: 'example',
  examplePort: 8104,
  lib: 'lib',
  src: 'src',
  srcPort: 8100,
  templatePort: 8103,
  test: 'test',
  testPort: 8101
};

module.exports = config;
