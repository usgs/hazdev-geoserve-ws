<?php

include_once dirname(__FILE__) . '/config.inc.php';


// configure factory
$CLASSES_DIR = $APP_DIR . '/lib/classes';
include $CLASSES_DIR . '/PlacesQuery.class.php';
include $CLASSES_DIR . '/GeoserveFactory.class.php';
include $CLASSES_DIR . '/GeoserveWebService.class.php';

$FACTORY = new GeoserveFactory($CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
$SERVICE = new GeoserveWebService($FACTORY);
