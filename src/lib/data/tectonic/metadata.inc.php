<?php

$GEOSERVE_METADATA['tectonic'] = array(
  'name' => 'tectonic',
  'title' => 'Tectonic Summary',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'https://www.sciencebase.gov/catalog/item/5a6f547de4b06e28e9caca43',
  'description' => 'Tectonic regions with corresponding descriptions.',
  'fields' => array(
    'name' => array(
      'type' => 'String',
      'description' => 'Tectonic summary region name.'
    ),
    'summary' => array(
      'type' => 'String',
      'description' => 'Tectonic summary content.'
    ),
    'type' => array(
      'type' => 'String',
      'description' => 'Tectonic summary type.'
    )
  )
);
