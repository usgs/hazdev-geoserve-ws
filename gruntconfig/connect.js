'use strict';

var config = require('./config');


var connect = {
  options: {
    hostname: '*'
  },

  rules: [
    {
      from: '^(' + config.ini.MOUNT_PATH + ')?/(services|places|regions|layers)\\.(json)\\??(.*)$',
      to: '/$2.php?format=$3&$4'
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
      open: 'http://127.0.0.1:' + config.srcPort + config.ini.MOUNT_PATH + '/',
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
        config.etc,
        'node_modules'
      ],
      port: config.testPort,
      open: 'http://localhost:' + config.testPort + '/test.html'
    }
  },

  example: {
    options: {
      base: [
        config.build + '/' + config.src + '/htdocs',
        config.example,
        config.build + '/' + config.test,
        config.etc
      ],
      open: 'http://localhost:' + config.examplePort + '/example.html',
      port: config.examplePort
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
