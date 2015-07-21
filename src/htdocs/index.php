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


<h5>1.1.1 Circle Search</h5>
<ul class="parameters vertical separator no-style">
  <li id="latitude">
    <header>
      <code>latitude</code>
    </header>
    <p>Latitude in decimal degrees [-90,90].</p>
  </li>
  <li id="longitude">
    <header>
      <code>longitude</code>
    </header>
    <p>Longitude in decimal degrees [-180,180].</p>
  </li>
  <li id="maxradiuskm">
    <header>
      <code>maxradiuskm</code>
    </header>
    <p>
      Search radius (in kilometers) from the center point (latitude, longitude).
    </p>
  </li>
  <li id="circle_limit">
    <header>
      <code>limit</code>
      <small>optional</small>
    </header>
    <p>
      Limit number of results, sorted by distance.
    </p>
  </li>
  <li id="circle_minpopulation">
    <header>
      <code>minpopulation</code>
      <small>optional</small>
    </header>
    <p>
      Limit results to places where population is greater than, or equal
      to, minpopulation.
    </p>
  </li>
  <li id="circle_type">
    <header>
      <code>type</code>
      <small>optional</small>
    </header>
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

<h5>1.1.2 Rectangle Search</h5>
<ul class="parameters vertical separator no-style">
  <li id="maxlatitude">
    <header>
      <code>maxlatitude</code>
    </header>
    <p>Upper latitude bounds in decimal degrees [-90,90].</p>
  </li>
  <li id="minlatitude">
    <header>
      <code>minlatitude</code>
    </header>
    <p>Lower latitude bounds in decimal degrees [-90,90].</p>
  </li>
  <li id="maxlongitude">
    <header>
      <code>maxlongitude</code>
    </header>
    <p>Upper longitude bounds in decimal degrees [-180,180].</p>
  </li>
  <li id="minlongitude">
    <header>
      <code>minlongitude</code>
    </header>
    <p>Lower longitude bounds in decimal degrees [-180,180].</p>
  </li>
  <li id="rectangle_limit">
    <header>
      <code>limit</code>
      <small>optional</small>
    </header>
    <p>
      Limit number of results, sorted by population.
    </p>
  </li>
  <li id="rectangle_minpopulation">
    <header>
      <code>minpopulation</code>
      <small>optional</small>
    </header>
    <p>
      Limit results to places where population is greater than, or equal
      to, minpopulation.
    </p>
  </li>
  <li id="rectangle_type">
    <header>
      <code>type</code>
      <small>optional</small>
    </header>
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
      </dl>
    </div>
  </li>
</ul>


<h3>2. Response</h3>
<p>
    The response is formatted as one or more nested
    <a href="http://geojson.org/geojson-spec.html#feature-collection-objects">
    GeoJSON FeatureCollections</a>. Each nested GeoJSON FeatureCollections is
    keyed by the request <code>type</code>.
</p>

<h4>2.1 Properties</h4>
<p>
  Each returned Feature in the GeoJSON FeatureCollection includes an id,
  a geometry object with longitude, latitude, and elevation,
  and a properties object with the following attributes:
</p>

<ul class="parameters vertical separator no-style">
  <li id="admin1_code">
    <header>
      <code>admin1_code</code>
      <small class="type">string</small>
    </header>
    <p>
      Three character code for a primary administrative division of a country,
      such as a state in the United States.
    </p>
  </li>
  <li id="admin1_name">
    <header>
      <code>admin1_name</code>
      <small class="type">string</small>
    </header>
    <p>
      Name of a primary administrative division of a country, such as a state
      in the United States.
    </p>
  </li>
  <li id="azimuth">
    <header>
      <code>azimuth</code>
      <small class="type">decimal</small>
    </header>
    <p>
      Direction in decimal degrees [0,360] from the Feature to the center
      point (latitude, longitude).
    </p>
  </li>
  <li id="country_code">
    <header>
      <code>country_code</code>
      <small class="type">string</small>
    </header>
    <p>
      ISO-3166 2-character country code
    </p>
  </li>
  <li id="country_name">
    <header>
      <code>country_name</code>
      <small class="type">string</small>
    </header>
    <p>
      Name of country.
    </p>
  </li>
  <li id="distance">
    <header>
      <code>distance</code>
      <small class="type">decimal</small>
    </header>
    <p>
      Distance in kilometers from the Feature to the center point(latitude,
      longitude).
    </p>
  </li>
  <li id="feature_class">
    <header>
      <code>feature_class</code>
      <small class="type">string</small>
    </header>
    <p>
      Geonames <a href="http://www.geonames.org/source-code/javadoc/org/geonames/FeatureClass.html">
      feature class</a> used to describe the Feature.
    </p>
  </li>
  <li id="feature_code">
    <header>
      <code>feature_code</code>
      <small class="type">string</small>
    </header>
    <p>
      Geonames <a href="http://www.geonames.org/export/codes.html">feature
      code</a> used to describe the Feature.
    </p>
  </li>
  <li id="geoname_id">
    <header>
      <code>id</code>
      <small class="type">integer</small>
    </header>
    <p>
     ID in geonames database.
    </p>
  </li>
  <li id="name">
    <header>
      <code>name</code>
      <small class="type">string</small>
    </header>
    <p>
      Name of the Feature.
    </p>
  </li>
  <li id="population">
    <header>
      <code>population</code>
      <small class="type">integer</small>
    </header>
    <p>
      Population associated with the Feature.
    </p>
  </li>
</ul>

<h3>3. Example</h3>
<p>
  Below are example resquests and responses that detail the nested GeoJSON
  structure. Each type has a nested GeoJSON FeatureCollection that may contain
  multiple GeoJSON features.
</p>

<h4>3.1 Requests</h4>
<p>
  The example requests are separated by type. There are examples for both
  geonames and event type requests.
</p>

<h5>3.1.1 Geonames Type</h5>
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
  <li>
    <p>All places within a rectangle with at least 10,000 people</p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/places?minlatitude=39&maxlatitude=40&minlongitude=-105&maxlongitude=106&minpopulation=10000&type=geonames';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
</ul>

<h5>3.1.1 Event Type</h5>
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

<h4>3.2 Responses</h4>
<p>
  The &ldquo;generic response&rdquo; details the data and structure returned by
  the web sevice, while the &ldquo;sample response&rdquo; depicts an actual
  response from the Geoserve API.
</p>

<div class="row">
  <div class="column one-of-two">
    <h5>3.2.1 Pseudocode Response</h5>
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
    <h5>3.2.2 Actual Response</h5>
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
