<?php

// entry point into the FDSN Event Webservice

if (!isset($TEMPLATE)) {
  include_once 'functions.inc.php';

  try {
    // configuration
    include_once '../conf/service.inc.php';

    // caching headers
    $CACHE_MAXAGE = 900;
    include_once '../lib/cache.inc.php';

    $usage = false;
    $method = param('method');
    if ($method === 'places') {
      $SERVICE->places();
    } else {
      $usage = true;
    }
  } catch (Exception $e) {
    $SERVICE->error(503, $e->getMessage());
  }

  if (!$usage) {
    return;
  }


  $TITLE = 'API Documentation - Geoserve';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="geoserve.css"/>';

  include 'template.inc.php';
}
?>


<h2>Places</h2>

<h3>URL</h3>
<?php
  echo '<code>',
      $HOST_URL_PREFIX, $MOUNT_PATH, '/places',
      '?<em>parameters</em>',
      '</code>';
?>


<h3>Examples</h3>

<h4>Five nearest places to a point with at least 1,000 people</h4>
<?php
  $url = $HOST_URL_PREFIX . $MOUNT_PATH .
      '/places?latitude=34&longitude=-118&limit=5&minpopulation=1000';
  echo '<p><a href="', $url, '">', $url, '</a></p>';
?>

<h4>All places within 200km of a point with at least 1,000 people</h4>
<?php
  $url = $HOST_URL_PREFIX . $MOUNT_PATH .
      '/places?latitude=34&longitude=-118&maxradiuskm=200&minpopulation=1000';
  echo '<p><a href="', $url, '">', $url, '</a></p>';
?>



<h3>Parameters</h3>
<p>
  All parameters must be passed in the query string.
  Only listed parameters may be used, others will generate an error.
  Empty values for these parameters are supported.
</p>

<dl>
  <dt><code>latitude</code></dt>
  <dd>
    Latitude in decimal degrees.
    [-90, 90].
    Required.
  </dd>

  <dt><code>longitude</code></dt>
  <dd>
    Longitude in decimal degrees.
    [-180, 180].
    Required.
  </dd>

  <dt><code>limit</code></dt>
  <dd>
    Only return this many places.

    Optional, although one of <code>limit</code> and <code>maxdistancekm</code> is required.
  </dd>

  <dt><code>maxdistancekm</code></dt>
  <dd>
    Maximum distance from <code>latitude</code>, <code>longitude</code>
    to place in kilometers.

    Optional, although one of <code>limit</code> and <code>maxdistancekm</code> is required.
  </dd>

  <dt><code>minpopulation</code></dt>
  <dd>
    Only return places with a minimum of this number of people.
  </dd>
</dl>


<h3>Output</h3>

<p>A <a href="http://geojson.org/geojson-spec.html#feature-collection-objects">
    GeoJSON FeatureCollection</a> of place Feature objects.</p>

<p>Each matching place Feature includes geometry with
    <code>longitude</code>, <code>latitude</code>, and <code>elevation</code>,
    as well as the following properties:</p>

<dl>
  <dt><code>admin1_code</code></dt>
  <dd>First administrative region.  In the United States, this is the state.</dd>

  <dt><code>azimuth</code></dt>
  <dd>Direction in decimal degrees from search point to place.</dd>

  <dt><code>distance</code></dt>
  <dd>Distance in meters from search point to place.</dd>

  <dt><code>name</code></dt>
  <dd>Place name</dd>

  <dt><code>population</code></dt>
  <dd>Population</dd>
</dl>
