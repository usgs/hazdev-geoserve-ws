#!/bin/bash -ex

pushd $(dirname $0) > /dev/null 2>&1;

#
# -c INI configuration file
# -t Document root
# -S Start a built-in PHP server on host:port
# router.php to configure 'rewrites'
#
php \
  -c php.ini \
  -t src/htdocs \
  -S localhost:9040 \
  $(pwd)/router.php


popd > /dev/null 2>&1;

exit 0;
