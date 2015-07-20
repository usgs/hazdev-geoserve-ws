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

<h3>1. Request</h3>
<p>
  A geoserve <em>places</em> search takes the following form:
</p>
<?php
  echo '<pre><code>',
      $HOST_URL_PREFIX, $MOUNT_PATH, '/places',
      '?<em>parameters</em>',
      '</code></pre>';
?>

<h4>1.1 Parameters</h4>
<p>
  When performing a places search the bounds can be limited by defining a
  circle (latitude, longitude, and maxradiuskm) or by defining a rectangle
  (minlatitude, maxlatitude, minlongitude, maxlongitude).
</p>


<div class="row">
  <div class="column one-of-two">
    <h5>1.1.1 Circle Search</h5>
    <ul class="parameters vertical separator no-style">
      <li>
        <span>
          <code>latitude</code>
        </span>
        <p>Latitude in decimal degrees [-90,90].</p>
      </li>
      <li>
        <span>
          <code>longitude</code>
        </span>
        <p>Longitude in decimal degrees [-180,180].</p>
      </li>
      <li>
        <span>
          <code>maxradiuskm</code>
        </span>
        <p>
          Search radius (in kilometers) from the center point (latitude, longitude).
        </p>
      </li>
      <li>
        <span>
          <code>limit</code>
          <small class="optional">optional</small>
        </span>
        <p>
          Limit number of results, sorted by distance.
        </p>
      </li>
      <li>
        <span>
          <code>minpopulation</code>
          <small class="optional">optional</small>
        </span>
        <p>
          Limit results to places where population is greater than, or equal
          to, minpopulation.
        </p>
      </li>
      <li>
        <span>
          <code>type</code>
          <small class="optional">optional</small>
        </span>
        <div>
          <p>
            The type of search being performed.
          </p>
          <dl class="vertical places-type">
            <dt>
              <code>type=geonames</code>
            </dt>
            <dd>
              A generic query where any combination of parameters may be
              specified.
            </dd>
            <dt>
              <code>type=event</code>
            </dt>
            <dd>
              Returns the five nearby places displayed on the earthquake
              event pages. All parameters other than latitude/longitude
              are ignored.
            </dd>
          </dl>
        </div>
      </li>
    </ul>
  </div>

  <div class="column one-of-two">
    <h5>1.1.2 Rectangle Search</h5>
    <ul class="parameters vertical separator no-style">
      <li>
        <span>
          <code>maxlatitude</code>
        </span>
        <p>Upper latitude bounds in decimal degrees [-90,90].</p>
      </li>
      <li>
        <span>
          <code>minlatitude</code>
        </span>
        <p>Latitude in decimal degrees [-90,90].</p>
      </li>
      <li>
        <span>
          <code>maxlongitude</code>
        </span>
        <p>Longitude in decimal degrees [-180,180].</p>
      </li>
      <li>
        <span>
          <code>minlongitude</code>
        </span>
        <p>Longitude in decimal degrees [-180,180].</p>
      </li>
      <li>
        <span>
          <code>limit</code>
          <small class="optional">optional</small>
        </span>
        <p>
          Limit number of results, sorted by population.
        </p>
      </li>
      <li>
        <span>
          <code>minpopulation</code>
          <small class="optional">optional</small>
        </span>
        <p>
          Limit results to places where population is greater than, or equal
          to, minpopulation.
        </p>
      </li>
      <li>
        <span>
          <code>type</code>
          <small class="optional">optional</small>
        </span>
        <div>
          <p>
            The type of search being performed.
          </p>
          <dl class="vertical places-type">
            <dt>
              <code>type=geonames</code>
            </dt>
            <dd>
              A generic query where any combination of parameters may be
              specified.
            </dd>
            <dt>
              <code>type=event</code>
            </dt>
            <dd>
              Returns the five nearby places displayed on the earthquake
              event pages. All parameters other than latitude/longitude
              are ignored.
            </dd>
          </dl>
        </div>
      </li>
    </ul>
  </div>
</div>

<h3>2. Response</h3>
<p>
    The web service response is formatted as one or more nested
    <a href="http://geojson.org/geojson-spec.html#feature-collection-objects">
    GeoJSON FeatureCollections</a>. Each of these nested GeoJSON
    FeatureCollections are keyed by &ldquo;type&rdquo;.
</p>

<h4>2.1 Properties</h4>
<p>
  Each matching Feature includes an <code>id</code>, a geometry object with
  <code>longitude</code>, <code>latitude</code>, and <code>elevation</code>,
  and a properties object with the following attributes:
</p>


<ul class="parameters vertical separator no-style">
  <li>
    <span>
      <code>admin1_code</code>
      <small class="type">string</small>
    </span>
    <p>
      Three character code for a primary administrative division of a country,
      such as a state in the United States.
    </p>
  </li>
  <li>
    <span>
      <code>admin1_name</code>
      <small class="type">string</small>
    </span>
    <p>
      Name of a primary administrative division of a country, such as a state
      in the United States.
    </p>
  </li>
  <li>
    <span>
      <code>azimuth</code>
      <small class="type">decimal</small>
    </span>
    <p>
      Direction in decimal degrees [0,360] from the Feature to the center
      point (latitude, longitude).
    </p>
  </li>
  <li>
    <span>
      <code>country_code</code>
      <small class="type">string</small>
    </span>
    <p>
      Two character code for country.
    </p>
  </li>
  <li>
    <span>
      <code>country_name</code>
      <small class="type">string</small>
    </span>
    <p>
      Name of country.
    </p>
  </li>
  <li>
    <span>
      <code>distance</code>
      <small class="type">decimal</small>
    </span>
    <p>
      Distance in kilometers from the Feature to the center point(latitude,
      longitude).
    </p>
  </li>
  <li>
    <span>
      <code>feature_class</code>
      <small class="type">string</small>
    </span>
    <p>
      Geonames <a href="http://www.geonames.org/source-code/javadoc/org/geonames/FeatureClass.html">
      feature class</a> used to describe the Feature.
    </p>
  </li>
  <li>
    <span>
      <code>feature_code</code>
      <small class="type">string</small>
    </span>
    <p>
      Geonames <a href="http://www.geonames.org/export/codes.html">feature
      code</a> used to describe the Feature.
    </p>
  </li>
  <li>
    <span>
      <code>name</code>
      <small class="type">string</small>
    </span>
    <p>
      Name of the Feature.
    </p>
  </li>
  <li>
    <span>
      <code>population</code>
      <small class="type">integer</small>
    </span>
    <p>
      Population associated with the Feature.
    </p>
  </li>
</ul>

<h3>3. Example</h3>

<h4>3.1 Example Requests</h4>
<p>
  Below are example resquests and responses that detail the nested GeoJSON
  structure. Each type has a nested GeoJSON FeatureCollection that may contain
  multiple GeoJSON features.
</p>

<h5>3.1.1 Geonames Type Requests</h5>
<ul>
  <li>
    <p>Five nearest places within 100km of a point</p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/places?latitude=39.75&amp;longitude=-105.2&amp;maxradiuskm=100&amp;limit=5&amp;type=geonames';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
  <li>
    <p>All places within 100km of a point with at least 1,000 people</p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/places?latitude=39.75&amp;longitude=-105.2&amp;maxradiuskm=100&amp;minpopulation=1000&amp;type=geonames';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
</ul>

<h5>3.1.1 Event Type Request</h5>
<ul>
  <li>
    <p>An event type request that always returns 5 places near a point</p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/places?latitude=45.1&amp;longitude=-70.1&amp;type=event';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
</ul>

<h4>3.2 Example Responses</h4>
<p>
  The &ldquo;generic response&rdquo; details the data and structure returned by
  the web sevice, while the &ldquo;sample response&rdquo; depicts an actual
  response from the Geoserve API.
</p>

<div class="row">
  <div class="column one-of-two">
    <h5>3.2.1 Generic Response</h5>
    <pre><code>{
  metadata: {
    request: &lt;web service request URL&gt;,
    submitted: &lt;ISO 8601 Timestamp&gt;,
    types: [
      &lt;places type&gt;, ...
    ],
    version: &lt;web service version number&gt;
  },
  &lt;type&gt;: {
    type: "FeatureCollection",
    count: &lt;count&gt;,
    features: [
      {
        type: "Feature",
        id: &lt;id&gt;,
        geometry: {
          type: "Point",
          coordinates: [
            &lt;longitude&gt;,
            &lt;latitude&gt;,
            &lt;elevation&gt;
          ]
        },
        properties: {
          &lt;feature properties&gt;,
          ...
        }
      }
    ]
  }
}</code></pre>

  </div>
  <div class="column one-of-two">
    <h5>3.2.2 Sample Respons</h5>
    <pre><code>{
  metadata: {
    request: "/ws/geoserve/places?latitude=45.1&amp;longitude=-70.1&amp;maxradiuskm=10&amp;type=geonames",
    submitted: "2015-07-20T14:32:50+00:00",
    types: [
      "geonames"
    ],
    version: "0.0.1",
  },
  geonames: {
    type: "FeatureCollection",
    count: 1,
    features: [
      {
        type: "Feature",
        id: 4960263,
        geometry: {
          type: "Point",
          coordinates: [
            -70.21201,
            45.07783,
            254
          ]
        },
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
        }
      }
    ]
  }
}</code></pre>

  </div>
</div>
