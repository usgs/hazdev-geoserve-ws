<?php

$GEOSERVE_METADATA['timezones'] = array(
  'name' => 'timezones',
  'contact' => 'mhearne@usgs.gov',
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/timezones/',
  'description' => 'A dataset that, to the best of our knowledge, describes ' .
      'the spatial extent of the various time zones of the world.',
  'fields' => array(
    'dstend' => array(
      'type' => 'String',
      'description' => 'ISO 8601 timestamp when daylight savings time ends.'
    ),
    'dstoffset' => array(
      'type' => 'String',
      'description' => 'UTC offset in minutes during DST.'
    ),
    'dststart' => array(
      'type' => 'String',
      'description' => 'ISO 8601 timestamp when daylight savings time begins.'
    ),
    'name' => array(
      'type' => 'String',
      'description' => 'Timezone name.'
    ),
    'offset' => array(
      'type' => 'String',
      'description' => 'UTC offset in minutes during standard time.'
    )
  )
);
