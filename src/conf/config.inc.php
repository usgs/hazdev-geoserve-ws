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

// configure factory
include $APP_DIR . '/lib/classes/GeoserveFactory.class.php';
$FACTORY = new GeoserveFactory($CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
