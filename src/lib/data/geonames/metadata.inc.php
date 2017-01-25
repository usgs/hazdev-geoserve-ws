<?php

$GEOSERVE_METADATA['geonames'] = array(
  'name' => 'geonames',
  'contact' => 'TODO',
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/geonames/',
  'description' => 'Data were created by ' .
      '<a href="http://www.geonames.org/">Geonames</a> and can be found ' .
      '<a href="http://download.geonames.org/export/dump/">here</a>.',
  'fields' => array(
    'admin1_code' => array(
      'type' => 'String',
      'description' => 'Three character code for primary administrative ' .
          'division of a country, such as a state in the United States.'
    ),
    'admin1_name' => array(
      'type' => 'String',
      'description' => 'Name of a primary administrative division of a ' .
          'country, such as a state in the United States.'
    ),
    'azimuth' => array(
      'type' => 'Number',
      'description' => 'Direction (in decimal degrees [0, 360]) from the ' .
          'Feature to the center point (latitude, longitude).'
    ),
    'country_code' => array(
      'type' => 'String',
      'description' => 'ISO-3166 2-character country code.'
    ),
    'country_name' => array(
      'type' => 'String',
      'description' => 'Name of country.'
    ),
    'distance' => array(
      'type' => 'Number',
      'description' => 'Distance (in kilometers) from the Feature to the ' .
          'center point (latitude, longitude).'
    ),
    'feature_class' => array(
      'type' => 'String',
      'description' => 'Geonames <a href="">feature class</a> used to ' .
          'describe the Feature.'
    ),
    'feature_code' => array(
      'type' => 'String',
      'description' => 'Geonames <a href="">feature code</a> used to ' .
          'describe the Feature.'
    ),
    'id' => array(
      'type' => 'Number',
      'description' => 'ID in geonames database.'
    ),
    'name' => array(
      'type' => 'String',
      'description' => 'Name of the Feature.'
    ),
    'population' => array(
      'type' => 'Number',
      'description' => 'Population associated with the Feature.'
    )
  )
);
