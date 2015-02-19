'use strict';

var config = require('./config');


var connect = {
  options: {
    hostname: '*'
  },
  proxies: [
    {
      context: '/theme/',
      host: 'localhost',
      port: 8103,
      rewrite: {
        '^/theme': ''
      }
    }
  ],

  dev: {
    options: {
      base: [config.build + '/' + config.src + '/htdocs'],
      port: 8100,
      livereload: true,
      open: 'http://localhost:8100/index.php',
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(
          require('grunt-connect-proxy/lib/utils').proxyRequest,
          require('gateway')(options.base[0], {
            '.php': 'php-cgi',
            'env': {
              'PHPRC': 'node_modules/hazdev-template/dist/conf/php.ini'
            }
          })
        );
        return middlewares;
      }
    }
  },
  dist: {
    options: {
      base: [config.dist + '/htdocs'],
      port: 8102,
      keepalive: true,
      open: 'http://localhost:8102/',
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(
          require('grunt-connect-proxy/lib/utils').proxyRequest,
          require('gateway')(options.base[0], {
            '.php': 'php-cgi',
            'env': {
              'PHPRC': 'node_modules/hazdev-template/dist/conf/php.ini'
            }
          })
        );
        return middlewares;
      }
    }
  },
  template: {
    options: {
      base: ['node_modules/hazdev-template/dist/htdocs'],
      port: 8103
    }
  }
};


module.exports = connect;
