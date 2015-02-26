'use strict';

var config = require('./config');


var connect = {
  options: {
    hostname: '*'
  },

  rules: [
    {
      from: '^(' + config.ini.MOUNT_PATH + ')?/(places)\\??(.*)$',
      to: '/index.php?method=$2&$3'
    },
    {
      from: '^' + config.ini.MOUNT_PATH + '/(.*)$',
      to: '/$1'
    }
  ],

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
      open: 'http://localhost:8100/',
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(
          require('grunt-connect-rewrite/lib/utils').rewriteRequest,
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
          require('grunt-connect-rewrite/lib/utils').rewriteRequest,
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
