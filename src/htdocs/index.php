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
    } else if ($method === 'regions') {
      $SERVICE->regions($_GET);
    } else {
      $usage = true;
    }
  } catch (Exception $e) {
    // trigger error, this will go to logs in production
    trigger_error($e->getMessage());
    // output generic error message.
    $SERVICE->error(500, 'Server Error');
  }

  if (!$usage) {
    return;
  }


  $TITLE = 'Geoserve API Documentation';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="index.css"/>';

  include 'template.inc.php';
}
?>


<h2>Places</h2>
<p>
  A geoserve <em>places</em> search takes the following form:
</p>
<?php
  echo '<pre><code>',
      $HOST_URL_PREFIX, $MOUNT_PATH, '/places',
      '?<em>parameters</em>',
      '</code></pre>';
?>
<p>
  The data returned from this portion of the web service is from the
  <a href="http://www.geonames.org/">Geonames geographical database</a>.
</p>


<h3>Request Parameters</h3>
<p>
  All parameters must be passed in the query string.
  Only listed parameters may be used, others will generate an error.
  Empty values for these parameters are supported.
</p>


<h4>Required Parameters</h4>
<table class="tabular parameters responsive">
  <thead>
    <tr>
      <th>parameter</th>
      <th>type</th>
      <th>description</th>
    </tr>
  </thead>
  <tbody class="no-header-style">
    <tr id="latitude">
      <th>
        <code>latitude</code>
      </th>
      <td>Decimal</td>
      <td>
        Latitude in decimal degrees. [-90,90] degrees.
      </td>
    </tr>
    <tr id="longitude">
      <th><code>longitude</code></th>
      <td>Decimal</td>
      <td>
        Longitude in decimal degrees. [-180,180] degrees.
      </td>
    </tr>
    <tr id="maxradiuskm">
      <th><code>maxradiuskm</code></th>
      <td>Decimal</td>
      <td>
        Search radius (in kilometers) from
        <code>latitude</code>, <code>longitude</code>.
      </td>
    </tr>
  </tbody>
</table>


<h4>Optional Parameters</h4>
<table class="tabular parameters responsive">
  <thead>
    <tr>
      <th>parameter</th>
      <th>type</th>
      <th>description</th>
    </tr>
  </thead>
  <tbody class="no-header-style">
    <tr id="type">
      <th><code>type</code></th>
      <td>String</td>
      <td>
        The type of search being performed.

        <dl class="vertical places-type">
          <dt>geonames [default]</dt>
          <dd>
            A generic query where any combination of parameters may be
            specified.
          </dd>
          <dt>event</dt>
          <dd>
            Returns the five nearby places displayed on the earthquake
            event pages. All parameters other than latitude/longitude
            are ignored.
          </dd>
        </dl>

        <span class="note">
          If type="event" is specified, then maxradiuskm is no
          longer a required field.
        </span>
      </td>
    </tr>
    <tr id="limit">
      <th><code>limit</code></th>
      <td>Integer</td>
      <td>
        Return at most this number of places, sorted by distance.
      </td>
    </tr>
    <tr id="minpopulation">
      <th><code>minpopulation</code></th>
      <td>Integer</td>
      <td>
        Only return places with a minimum of this number of people.
      </td>
    </tr>
  </tbody>
</table>


<h3>Response Properties</h3>
<p>
    A <a href="http://geojson.org/geojson-spec.html#feature-collection-objects">
    GeoJSON FeatureCollection</a> of place Feature objects.
</p>
<p>
  Each matching place Feature includes geometry with
  <code>longitude</code>, <code>latitude</code>, and <code>elevation</code>,
  as well as the following properties:
</p>
<table class="tabular parameters responsive">
  <thead>
    <tr>
      <th>property</th>
      <th>type</th>
      <th>description</th>
    </tr>
  </thead>
  <tbody class="no-header-style">
    <tr id="admin1_code">
      <th><code>admin1_code</code></th>
      <td>String</span></td>
      <td>
        First administrative region of the place.
        In the United States, this is the state.
      </td>
    </tr>
    <tr id="azimuth">
      <th><code>azimuth</code></th>
      <td>Decimal</td>
      <td>
        Direction in decimal degrees from search point to
        the place. [0, 360] degrees.
      </td>
    </tr>
    <tr id="distance">
      <th><code>distance</code></th>
      <td>Decimal</td>
      <td>
        Distance in meters from search point to the place.
      </td>
    </tr>
    <tr id="name">
      <th><code>name</code></th>
      <td>String</td>
      <td>
        Name of the place.
      </td>
    </tr>
    <tr id="population">
      <th><code>population</code></th>
      <td>Integer</td>
      <td>
        Population associated with the place.
      </td>
    </tr>
  </tbody>
</table>


<h3>Example Requests</h3>

<h4>Five nearest places within 100km of a point</h4>
<?php
  $url = $HOST_URL_PREFIX . $MOUNT_PATH .
      '/places?latitude=39.75&longitude=-105.2&maxradiuskm=100&limit=5';
  echo '<pre><code><a href="', $url, '">', $url, '</a></code></pre>';
?>

<h4>All places within 100km of a point with at least 1,000 people</h4>
<?php
  $url = $HOST_URL_PREFIX . $MOUNT_PATH .
      '/places?latitude=39.75&longitude=-105.2&maxradiuskm=100&minpopulation=1000';
  echo '<pre><code><a href="', $url, '">', $url, '</a></code></pre>';
?>

