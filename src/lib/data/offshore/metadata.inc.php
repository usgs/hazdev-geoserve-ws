<?php

$GEOSERVE_METADATA['offshore'] = array(
  'name' => 'offshore',
  'title' => 'Offshore',
  'contact' => 'TODO',
  'lastUpdated' => '2016-04-07T00:00:00Z',
  'raw' => 'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/offshore/',
  'description' => 'This dataset is from the original FE Plus dataset and ' .
      'defines "offshore" regions where earthquakes are a common occurrence.',
  'fields' => array(
    'name' => array(
      'type' => 'String',
      'description' => 'Offshore region name.'
    )
  )
);
