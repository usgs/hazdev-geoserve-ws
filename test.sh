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
  router.php > /dev/null 2>&1 &

php_pid=$!;

sleep 10;

result=`curl \
    --connect-timeout 5 \
    --max-time 10 \
    --retry 5 \
    --retry-delay 2 \
    --retry-max-time 5 \
    -s -o /dev/null \
    -w "%{http_code}" \
    http://localhost:9040/`;

status=1;

if [ "${result}" == '200' ]; then
  status=0;
fi

kill $php_pid;

popd > /dev/null 2>&1;

exit $status;
