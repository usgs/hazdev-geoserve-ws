<?php

date_default_timezone_set('UTC');

$APP_DIR = dirname(dirname(__FILE__));

// parse config
$CONFIG_INI_FILE = $APP_DIR . '/conf/config.ini';
if (!file_exists($CONFIG_INI_FILE)) {
  trigger_error('Application not configured. Run pre-install script.');
  exit(-1);
}
$CONFIG = parse_ini_file($CONFIG_INI_FILE);

// environment overrides configuration
$CONFIG = array_merge($CONFIG, $_ENV);


$forwarded_https = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// build absolute Geoserve page URL string
$server_protocol =
    (
      (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'Off')
      || $forwarded_https
    ) ? 'https://' : 'http://';
$server_host = isset($_SERVER['HTTP_HOST']) ?
    $_SERVER['HTTP_HOST'] : "earthquake.usgs.gov";
$server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
$server_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

$HOST_URL_PREFIX = $server_protocol . $server_host;
if ( ($server_port == 80 && ($server_protocol == 'http://' || $forwarded_https))
    || ($server_port == 443 && $server_protocol == 'https://') ) {
  // don't need port
} else {
  // if a port is specified in the HTTP_HOST, don't use twice (ex: localhost:8080, perhaps used in port forwarding)
  if(!strpos($server_host, ':')) {
    $HOST_URL_PREFIX .= ':' . $server_port;
  }
}

$MOUNT_PATH = $CONFIG['MOUNT_PATH'];
