'use strict';

var config = require('./config');


var connect = {
  options: {
    hostname: '*'
  },

  rules: [
    {
      from: '^(' + config.ini.MOUNT_PATH + ')?/(places|regions)\\??(.*)$',
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
      port: config.templatePort,
      rewrite: {
        '^/theme': ''
      }
    }
  ],

  dev: {
    options: {
      base: [config.build + '/' + config.src + '/htdocs'],
      port: config.srcPort,
      livereload: true,
      open: true,
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

  test: {
    options: {
      base: [
        config.build + '/' + config.src + '/htdocs',
        config.build + '/' + config.test,
        'node_modules'
      ],
      port: config.testPort,
      open: 'http://localhost:' + config.testPort + '/test.html'
    }
  },

  dist: {
    options: {
      base: [config.dist + '/htdocs'],
      port: config.distPort,
      keepalive: true,
      open: true,
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
      port: config.templatePort
    }
  }
};


module.exports = connect;
