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
      $SERVICE->places($_GET);
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


  $TITLE = 'Places Documentation';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="index.css"/>';

  include 'template.inc.php';
}
?>

<p>
  The places enpoint allows users to search for places within a certian
  distance of a geographical point (circle search), or users can search for
  places within a latitude/longitudinal range (rectangle/box search).
</p>

<p>
  Data returned by the Geoserve Web Service <em>places</em> endpoint is
  provided by the <a href="http://www.geonames.org/">Geonames</a> geographical
  database.
</p>

<h3>Requests</h3>
<p>
  A geoserve <em>places</em> search takes the following form:
</p>
<?php
  echo '<pre><code>',
      $HOST_URL_PREFIX, $MOUNT_PATH, '/places',
      '?<em>parameters</em>',
      '</code></pre>';
?>
<h4>Examples</h4>

<p>Five nearest places within 100km of a point</p>
<?php
  $url = $HOST_URL_PREFIX . $MOUNT_PATH .
      '/places?latitude=39.75&longitude=-105.2&maxradiuskm=100&limit=5';
  echo '<a href="', $url, '">', $url, '</a>';
?>

<p>All places within 100km of a point with at least 1,000 people</p>
<?php
  $url = $HOST_URL_PREFIX . $MOUNT_PATH .
      '/places?latitude=39.75&longitude=-105.2&maxradiuskm=100&minpopulation=1000';
  echo '<a href="', $url, '">', $url, '</a>';
?>

<h4>Required Parameters</h4>
<p>
  When performing a places search the bounds can be limited by defining a
  circle (latitude, longitude, and maxradiuskm) or by defining a rectangle
  (minlatitude, maxlatitude, minlongitude, maxlongitude).
</p>


<div class="row">
  <div class="column one-of-two">
    <h5>Circle</h5>
    <table class="tabular parameters responsive">
      <thead>
        <tr>
          <th>parameter</th>
          <th>description</th>
        </tr>
      </thead>
      <tbody class="no-header-style">
        <tr id="latitude">
          <th>
            <code>latitude</code>
          </th>
          <td>
            Latitude in decimal degrees [-90,90].
          </td>
        </tr>
        <tr id="longitude">
          <th><code>longitude</code></th>
          <td>
            Longitude in decimal degrees [-180,180].
          </td>
        </tr>
        <tr id="maxradiuskm">
          <th><code>maxradiuskm</code></th>
          <td>
            Search radius (in kilometers) from the center point
            (latitude, longitude).
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="column one-of-two">
    <h5>Rectangle</h5>
    <table class="tabular parameters responsive">
      <thead>
        <tr>
          <th>parameter</th>
          <th>description</th>
        </tr>
      </thead>
      <tbody class="no-header-style">
        <tr id="minlatitude">
          <th>
            <code>minlatitude</code>
          </th>
          <td>
            Latitude in decimal degrees [-90,90].
          </td>
        </tr>
        <tr id="maxlatitude">
          <th>
            <code>maxlatitude</code>
          </th>
          <td>
            Latitude in decimal degrees [-90,90].
          </td>
        </tr>
        <tr id="minlongitude">
          <th><code>minlongitude</code></th>
          <td>
            Longitude in decimal degrees [-180,180].
          </td>
        </tr>
        <tr id="maxlongitude">
          <th><code>maxlongitude</code></th>
          <td>
            Longitude in decimal degrees [-180,180].
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>


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
    <tr id="admin1_name">
      <th><code>admin1_name</code></th>
      <td>String</span></td>
      <td>
        TODO
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
    <tr id="country_code">
      <th><code>country_code</code></th>
      <td>String</td>
      <td>
        TODO
      </td>
    </tr>
    <tr id="country_name">
      <th><code>country_name</code></th>
      <td>String</td>
      <td>
        TODO
      </td>
    </tr>
    <tr id="distance">
      <th><code>distance</code></th>
      <td>Decimal</td>
      <td>
        Distance in meters from search point to the place.
      </td>
    </tr>
    <tr id="feature_class">
      <th><code>feature_class</code></th>
      <td>String</td>
      <td>
        TODO
      </td>
    </tr>
    <tr id="feature_code">
      <th><code>feature_code</code></th>
      <td>String</td>
      <td>
        TODO
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


<h3>Example Output</h3>
<p>
  Below is some example output that shows the nested GeoJSON structure.
  Each type has a nested GeoJSON FeatureCollection that may contain multiple
  GeoJSON features.
</p>

<div class="row">
  <div class="column one-of-two">
    <h4>Generic Output</h4>
    <pre><code>{
  &lt;type&gt;: {
    type: "FeatureCollection",
    features: [
      {
        type: "Feature",
        id: &lt;id&gt;,
        properties: {
          &lt;feature properties&gt;,
          ...
        },
        geometry: {
          type: "Point",
          coordinates: [
            &lt;longitude&gt;,
            &lt;latitude&gt;,
            &lt;elevation&gt;
          ]
        },
        metadata: {
          count: &lt;count&gt;
        }
      }
    ]
  },
  metadata: {
    generated: &lt;millisecond timestamp&gt;
    status: &lt;HTTP status code&gt;,
    url: &lt;web service request URL&gt;
    version: &lt;web service version number&gt;
    types: [
      &lt;point type&gt;, ...
    ]
  }
}</pre></code>

  </div>
  <div class="column one-of-two">
    <h4>Sample Output</h4>
    <pre><code>{
  geonames: {
    type: "FeatureCollection",
    features: [
      {
        type: "Feature",
        id: 4960263,
        properties: {
          admin1_code: "ME",
          admin1_name: "Maine",
          azimuth: 254.4,
          country_code: "US",
          country_name: "United States",
          distance: 9.156,
          feature_class: "P",
          feature_code: "PPL",
          name: "Carrabassett",
          population: 0
        },
        geometry: {
          type: "Point",
          coordinates: [
            -70.21201,
            45.07783,
            254
          ]
        }
      }
    ],
    metadata: {
      count: 1
    }
  },
  metadata: {
    generated: "1436986115000",
    status: 200,
    url: "http://localhost:8100/ws/geoserve/places?latitude=45.1&amp;longitude=-70.1&amp;maxradiuskm=10",
    version: "0.0.1",
    types: [
      "geonames"
    ]
  }
}</code></pre>

  </div>
</div>
