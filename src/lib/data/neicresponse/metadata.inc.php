<?php

$GEOSERVE_METADATA['neicresponse'] = array(
  'name' => 'neicresponse',
  'title' => 'NEIC Response',
  'contact' => '', // TODO
  'lastUpdated' => '2015-09-21T00:00:00Z',
  'raw' => 'https://www.sciencebase.gov/catalog/item/5a6f547de4b06e28e9caca43',
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
