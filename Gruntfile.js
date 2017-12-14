'use strict';

module.exports = function (grunt) {

  var gruntConfig = require('./gruntconfig');

  gruntConfig.tasks.forEach(grunt.loadNpmTasks);
  grunt.initConfig(gruntConfig);


  grunt.registerTask('build', [
    'clean:build',
    'postcss:dev',
    'copy:build'
  ]);

  grunt.registerTask('builddist', [
    'build',
    'clean:dist',
    'copy:dist',
    'postcss:dist'
  ]);

  grunt.registerTask('dist', [
    'builddist',
    'connect:template',
    'configureRewriteRules',
    'configureProxies:dist',
    'connect:dist'
  ]);

  grunt.registerTask('default', [
    'configureRewriteRules',
    'configureProxies:dev',
    'build',
    'connect:template',
    'connect:dev',
    'watch'
  ]);

};
