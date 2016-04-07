<?php

$GEOSERVE_REGIONS = array(
  'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/regions.json?{parameters}",
  'notes' => array(
  ),
  'parameters' => array(
    'required' => array(
      'latitude' => array(
        'type' => 'Number',
        'minimum' => -90.0,
        'maximum' => 90.0,
        'description' => 'Latitude in decimal degrees of point.'
      ),
      'longitude' => array(
        'type' => 'Number',
        'minimum' => -180.0,
        'maximum' => 180.0,
        'description' => 'Longitude in decimal degrees of point.'
      )
    ),
    'optional' => array(
      'includeGeometry' => array(
        'type' => 'Boolean',
        'description' => 'Set to true returns poloygon points of the ' .
            'selected region. Default false.'
      ),
      'type' => array(
        'type' => 'Enumeration',
        'description' => 'Region types. Comma-separated list.',
        'values' => array(
          array(
            'name' => $GEOSERVE_METADATA['admin']['name'],
            'description' => $GEOSERVE_METADATA['admin']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['auth']['name'],
            'description' => $GEOSERVE_METADATA['auth']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['fe']['name'],
            'description' => $GEOSERVE_METADATA['fe']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['neiccatalog']['name'],
            'description' => $GEOSERVE_METADATA['neiccatalog']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['neicresponse']['name'],
            'description' => $GEOSERVE_METADATA['neicresponse']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['offshore']['name'],
            'description' => $GEOSERVE_METADATA['offshore']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['tectonic']['name'],
            'description' => $GEOSERVE_METADATA['tectonic']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['timezone']['name'],
            'description' => $GEOSERVE_METADATA['timezone']['description']
          )
        )
      )
    )
  ),
  'output' => array(
    'admin' => $GEOSERVE_METADATA['admin']['fields'],
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
      'description' => 'Region search at latitude 39.5, longitude -105',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/regions.json?" .
          'latitude=39.5&amp;longitude=-105'
    ),
    array(
      'description' => 'Region search at latitude 39.5, longitude -105, ' .
          'includeGeometry set to true',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/regions.json?" .
          'latitude=39.5&amp;longitude=-105&amp;includeGeometry=true'
    ),
    array(
      'description' => 'Region search at latitude 39.5, longitude -105, ' .
          'type set to fe',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/regions.json?" .
          'latitude=39.5&amp;longitude=-105&amp;type=fe'
    )
  )
);
