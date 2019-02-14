<?php

$GEOSERVE_METADATA['admin'] = array(
  'name' => 'admin',
  'title' => 'Administrative',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'https://www.sciencebase.gov/catalog/item/5a6f547de4b06e28e9caca43',
  'description' => 'Global polygons for use in Geoserve are based on data ' .
      'from [http://www.gadm.org/](http://www.gadm.org/) version 2.0. The ' .
      'GADM data has been processed in GIS to only include the level 2 ' .
      'boundaries (state level). Originally these data contained boundaries ' .
      'to level 3 (county level), but current requirements did not need such ' .
      'granularity. This simplification enables faster queries.',
  'fields' => array(
    'country' => array(
      'type' => 'String',
      'description' => 'Name of country.'
    ),
    'iso' => array(
      'type' => 'String',
      'description' => 'Country code.'
    ),
    'region' => array(
      'type' => 'String',
      'description' => 'Name of region.'
    )
  )
);
