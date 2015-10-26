'use strict';

module.exports = function (grunt) {

  var gruntConfig = require('./gruntconfig');

  gruntConfig.tasks.forEach(grunt.loadNpmTasks);
  grunt.initConfig(gruntConfig);

  grunt.event.on('watch', function (action, filepath) {
    // Only lint the file that actually changed
    grunt.config(['jshint', 'scripts'], filepath);
  });

  grunt.registerTask('test', [
    'copy:test',
    'browserify:test',
    'connect:test',
    'mocha_phantomjs'
  ]);

  grunt.registerTask('build', [
    'clean:build',
    'postcss:dev',
    'copy:build',
    'copy:leaflet',
    'copy:locationview',
    'jshint:scripts',
    'browserify'
  ]);

  grunt.registerTask('dist', [
    'build',
    'clean:dist',
    'copy:dist',
    'postcss:dist',
    'uglify',
    'connect:template',
    'configureRewriteRules',
    'configureProxies:dist',
    'connect:dist'
  ]);

  grunt.registerTask('default', [
    'configureRewriteRules',
    'configureProxies:dev',
    'configureProxies:test',
    'build',
    'test',
    'connect:template',
    'connect:dev',
    'connect:example',
    'watch'
  ]);

};
