<?php

$GEOSERVE_METADATA['fe'] = array(
  'name' => 'fe',
  'title' => 'Flinn Engdahl',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'https://www.sciencebase.gov/catalog/item/5a6f547de4b06e28e9caca43',
  'description' => 'This dataset was created by Presgrave, B.W. to provide ' .
      'more specific region names when available. Like FE regions, these ' .
      'regions are also based on a 1x1 degree grid. Unlike the FE regions, ' .
      'no unique number was assigned to each region.',
  'fields' => array(
    'name' => array(
      'type' => 'String',
      'description' => 'Name of FE region.'
    ),
    'num' => array(
      'type' => 'String',
      'description' => 'FE region identification number.'
    )
  )
);
