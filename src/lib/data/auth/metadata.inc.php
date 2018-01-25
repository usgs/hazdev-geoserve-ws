<?php

$GEOSERVE_METADATA['auth'] = array(
  'name' => 'authoritative',
  'title' => 'Authoritative',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/auth/',
  'description' => 'This dataset was created by the USGS/NEIC (B. Presgrave ' .
      '& P.Earle)  to define the regions where using various seismic ' .
      'monitoring organization\'s earthquake solutions is either recommended ' .
      '(for international organizations) or required (for ANSS affiliated ' .
      'organizations).',
  'fields' => array(
    'name' => array(
      'type' => 'String',
      'description' => 'Name of place.'
    ),
    'network' => array(
      'type' => 'String',
      'description' => 'Name of network.'
    ),
    'type' => array(
      'type' => 'String',
      'description' => 'Type of authoritative region'
    )
  )
);
