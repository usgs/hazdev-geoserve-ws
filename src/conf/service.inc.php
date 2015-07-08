<?php

include_once dirname(__FILE__) . '/config.inc.php';


// configure factory
$CLASSES_DIR = $APP_DIR . '/lib/classes';

include_once $CLASSES_DIR . '/GeoserveWebService.class.php';
include_once $CLASSES_DIR . '/PlacesFactory.class.php';
include_once $CLASSES_DIR . '/RegionsFactory.class.php';

$PLACES_FACTORY = new PlacesFactory(
    $CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
$REGIONS_FACTORY = new RegionsFactory(
    $CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);

$SERVICE = new GeoserveWebService($PLACES_FACTORY, $REGIONS_FACTORY);
