<?php

$GEOSERVE_LAYERS = array(
  'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/layers.json?{parameters}",
  'notes' => array(
    'Underlying <a href="ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/">' .
    'data files are available via FTP</a> for each data layer.'
  ),
  'parameters' => array(
    'required' => array(
      'type' => array(
        'type' => 'Enumeration',
        'description' => 'Name of layer.',
        'values' => array(
          $GEOSERVE_METADATA['auth'],
          $GEOSERVE_METADATA['fe'],
          $GEOSERVE_METADATA['neiccatalog'],
          $GEOSERVE_METADATA['neicresponse'],
          $GEOSERVE_METADATA['offshore'],
          $GEOSERVE_METADATA['tectonic'],
          $GEOSERVE_METADATA['timezone']
        )
      )
    ),
  ),
  'output' => array(
    'auth' => $GEOSERVE_METADATA['auth']['fields'],
    'fe' => $GEOSERVE_METADATA['fe']['fields'],
    'neiccatalog' => $GEOSERVE_METADATA['neiccatalog']['fields'],
    'neicresponse' => $GEOSERVE_METADATA['neicresponse']['fields'],
    'offshore' => $GEOSERVE_METADATA['offshore']['fields'],
    'tectonic' => $GEOSERVE_METADATA['tectonic']['fields'],
    'timezone' => $GEOSERVE_METADATA['timezone']['fields']
  ),
  'examples' => array(
    array(
      'description' => 'All regions in the <code>neicresponse</code> layer',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/layers.json?type=neicresponse"
    ),
    array(
      'description' => 'All regions in the <code>timezone</code> layer',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/layers.json?type=timezone"
    )
  )
);
