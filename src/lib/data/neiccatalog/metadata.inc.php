<?php

$GEOSERVE_METADATA['neiccatalog'] = array(
  'name' => 'neiccatalog',
  'title' => 'NEIC Catalog',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/neiccatalog/',
  'description' => 'This dataset was created by the USGS/NEIC (B. ' .
      'Presgrave & P.Earle) to define the polygon areas and threshold ' .
      'magnitudes at which the NEIC responds to and/or publishes an ' .
      'earthquake event to the public and/or NEIC event catalog.',
  'fields' => array(
    'magnitude' => array(
      'type' => 'String',
      'description' => 'Magnitude associated with the place.'
    ),
    'name' => array(
      'type' => 'String',
      'description' => 'NEIC catalog name.'
    )
  )
);
