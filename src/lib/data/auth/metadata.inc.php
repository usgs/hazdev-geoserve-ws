<?php

$GEOSERVE_METADATA['auth'] = array(
  'name' => 'authoritative',
  'title' => 'Authoritative',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'https://www.sciencebase.gov/catalog/item/5a6f547de4b06e28e9caca43',
  'description' => 'This dataset was created by the USGS/NEIC (B. Presgrave ' .
      '& P.Earle)  to define the regions where using various seismic ' .
      'monitoring organization\'s earthquake solutions is either recommended ' .
      '(for international organizations) or required (for ANSS affiliated ' .
      'organizations).',
  'fields' => array(
    'name' => array(
      'type' => 'String',
      'description' => 'Name of contributor.'
    ),
    'network' => array(
      'type' => 'String',
      'description' => 'Name of network.'
    ),
    'region' => array(
      'type' => 'String',
      'description' => 'Name of network sub-region'
    ),
    'type' => array(
      'type' => 'String',
      'description' => 'Type of authoritative region'
    ),
    'url' => array(
      'type' => 'String',
      'description' => 'URL for more information'
    )
  )
);

$GEOSERVE_METADATA['anss'] = $GEOSERVE_METADATA['auth'];
$GEOSERVE_METADATA['anss']['name'] = 'anss';
$GEOSERVE_METADATA['anss']['title'] = 'ANSS Authoritative Regions';
$GEOSERVE_METADATA['anss']['description'] = 'Subset of the ' .
    '<code>authoritative</code> layer, including only ' .
    '<a href="https://earthquake.usgs.gov/monitoring/anss/">' .
      'Advanced National Seismic System (ANSS)' .
    '</a> contributors.';
