<?php

$GEOSERVE_PLACES = array(
  'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/places.json?{parameters}",
  'description' => '<p>
      The places endpoint allows users to search for places within a certain
      distance of a geographical point (circle search), or users can search for
      places within a latitude/longitudinal range (rectangle/box search).
    </p>

    <p>
      Data returned by the Geoserve Web Service <em>places</em> endpoint is
      provided by the <a href="https://www.geonames.org/">Geonames</a>
      geographical database.
    </p>',
  'notes' => array(
    'Circle search (latitude, longitude, maxradiuskm) and rectangle search ' .
    '(maxlatitude, minlatitude, laxlongitude, minlongitude) are mutually ' .
    'exclusive. It is an error to specify both.',

    'Rectangle search does not support the <em>event</em> type.'
  ),
  'parameters' => array(
    'circle' => array(
      'latitude' => array(
        'type' => 'Number',
        'minimum' => -90.0,
        'maximum' => 90.0,
        'description' => 'Latitude in decimal degrees of center point.'
      ),
      'longitude' => array(
        'type' => 'Number',
        'minimum' => -180.0,
        'maximum' => 180.0,
        'description' => 'Longitude in decimal degrees of center point.'
      ),
      'maxradiuskm' => array(
        'type' => 'Number',
        'minimum' => 0,
        'maximum' => 6371,
        'description' => 'Search radius (in kilometers) from the center point.'
      )
    ),

    'rectangle' => array(
      'maxlatitude' => array(
        'type' => 'Number',
        'minimum' => -90.0,
        'maximum' => 90.0,
        'description' => 'Upper latitude bound in decimal degrees.'
      ),
      'minlatitude' => array(
        'type' => 'Number',
        'minimum' => -90.0,
        'maximum' => 90.0,
        'description' => 'Lower latitude bound in decimal degrees.'
      ),
      'maxlongitude' => array(
        'type' => 'Number',
        'minimum' => -180.0,
        'maximum' => 180.0,
        'description' => 'Upper longitude bound in decimal degrees.'
      ),
      'minlongitude' => array(
        'type' => 'Number',
        'minimum' => -180.0,
        'maximum' => 180.0,
        'description' => 'Lower longitude bound in decimal degrees.'
      )
    ),

    'optional' => array(
      'limit' => array(
        'type' => 'Number',
        'minimum' => 0,
        'maximum' => null,
        'description' => 'Limit number of results, sorted by distance. ' .
            'The event result type will always include 5 places regardless '.
            'of the requested limit.'
      ),
      'minpopulation' => array(
        'type' => 'Number',
        'minimum' => 0,
        'maximum' => null,
        'description' => 'Limit results to places where population is ' .
            'greater than or equal to minpopulation.'
      ),
      'type' => array(
        'type' => 'Enumeration',
        'description' => 'The type of search being performed. ' .
            'Comma-separated list.',
        'values' => array(
          array(
            'name' => $GEOSERVE_METADATA['geonames']['name'],
            'description' => $GEOSERVE_METADATA['geonames']['description']
          ),
          array(
            'name' => $GEOSERVE_METADATA['event']['name'],
            'description' => $GEOSERVE_METADATA['event']['description']
          )
        )
      )
    )
  ),
  'output' => array(
    'common' => $GEOSERVE_METADATA['geonames']['fields']
  ),
  'examples' => array(
    array(
      'description' => 'Five nearest places within 100km of a point',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/places.json?" .
          'latitude=39.75&amp;longitude=-105.2&amp;' .
          'maxradiuskm=100&amp;limit=5&amp;type=geonames'
    ),
    array(
      'description' => 'All places within 100km of a point with at least ' .
          '1,000 people',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/places.json?" .
          'latitude=39.75&amp;longitude=-105.2&amp;' .
          'maxradiuskm=100&amp;minpopulation=1000&amp;type=geonames'
    ),
    array(
      'description' => 'All places within a rectangle with at least 10,000 ' .
          'people',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/places.json?" .
          'minlatitude=39&amp;maxlatitude=40&amp;' .
          'minlongitude=-105&amp;maxlongitude=106&amp;' .
          'minpopulation=10000&amp;type=geonames'
    ),
    array(
      'description' => 'An event type request that always returns 5 places ' .
          'near a point',
      'url' => "${HOST_URL_PREFIX}${MOUNT_PATH}/places.json?" .
          'latitude=45.1&amp;longitude=-70.1&amp;type=event'
    )
  )
);
