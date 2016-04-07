<?php

include_once 'functions.inc.php';
include_once '../conf/config.inc.php';

$GEOSERVE_METADATA = array();

include_once 'admin/metadata.inc.php';
include_once 'auth/metadata.inc.php';
include_once 'event/metadata.inc.php';
include_once 'fe/metadata.inc.php';
include_once 'geonames/metadata.inc.php';
include_once 'neiccatalog/metadata.inc.php';
include_once 'neicresponse/metadata.inc.php';
include_once 'offshore/metadata.inc.php';
include_once 'tectonic/metadata.inc.php';
include_once 'timezones/metadata.inc.php';

$format = param('format', 'php');

$GEOSERVE_ENDPOINTS = array(
  array(
    'name' => 'Places',
    'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/places.${format}"
  ),
  array(
    'name' => 'Regions',
    'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/regions.${format}"
  ),
  array(
    'name' => 'Layers',
    'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/layers.${format}"
  )
);

$endpointLinks = array();
foreach ($GEOSERVE_ENDPOINTS as $endpoint) {
  $endpointLinks[] = navItem(str_replace($HOST_URL_PREFIX, '',
      $endpoint['url']), $endpoint['name'] . ' Service');
}

include_once 'places.inc.php';
include_once 'regions.inc.php';
include_once 'layers.inc.php';
