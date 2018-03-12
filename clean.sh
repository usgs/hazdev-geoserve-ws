#!/bin/bash -e

pushd $(dirname $0) > /dev/null 2>&1;

rm -rvf \
  node_modules \
  php.ini \
  src/conf/config.ini \
  src/conf/httpd.conf \
;

if [ -L src/htdocs/theme ]; then
  unlink src/htdocs/theme;
fi

popd > /dev/null 2>&1;

exit 0;
