<?php
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

  $TITLE = 'Regions Documentation';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="index.css"/>';

  include 'template.inc.php';
}
?>

<h2>1. Requests</h2>
<p>
  All parameters must be passed in the query string.
  Only listed parameters may be used, others will generate an error.
  Empty values for these parameters are not supported.
</p>

<p>
  A Regions Search takes the following form:
</p>
<?php
  echo '<pre><code>',
    $HOST_URL_PREFIX, $MOUNT_PATH, '/regions',
    '?<em>parameters</em>',
    '</code></pre>';
?>

<h3>1.1 Parameters</h3>
<ul class="parameters separator no-style">
  <li id="latitude">
    <header>
      <code>latitude</code>
    </header>
    <p>
      Latitude in decimal degrees [-90,90].
    </p>
  </li>
  <li id="longitude">
    <header>
      <code>Longitude</code>
    </header>
    <p>
      Longitude in decimal degrees [-180,180].
    </p>
  </li>
  <li id="includeGeometry">
    <header>
      <code>includeGeometry</code>
      <small>Optional</small>
    </header>
      <p>
        Set to true returns poloygon points of the selected region.
      </p>
  </li>
  <li id="type">
    <header>
      <code>type</code>
      <small>Optional</small>
    </header>
    <p>
      <ul>
        <li>
          <code>type=admin</code> shows admin regions.
        </li>
        <li>
          <code>type=authoritative</code> shows authoritative regions.
        </li>
        <li>
          <code>type=fe</code> shows fe regions.
        </li>
        <li>
          <code>type=neiccatalog</code> show neic catalog regions.
        </li>
        <li>
          <code>type=neicresponse</code> show neic neic regions.
        </li>
      </ul>
    </p>
  </li>
</ul>

<h2>2. Response</h2>
<p>
  The response is formatted as one or more nested
  <a href="http://geojson.org/geojson-spec.html#feature-collection-objects">
  GeoJSON FeatureCollections</a>. Each nested GeoJSON FeatureCollections is
  keyed by the request <code>type</code>.
</p>

<h3>2.1 Admin Regions</h3>
<ul class="admin-region parameters separator no-style">
  <li id="admin-iso">
    <header>
      <code>iso</code>
      <small class="type">String</small>
    </header>
    <p>
      Country Code.
    </p>
  </li>
  <li id="admin-country">
    <header>
      <code>Country</code>
      <small class="type">String</small>
    </header>
    <p>
      Name of Country.
    </p>
  </li>
  <li id="admin-region">
    <header>
      <code>Region</code>
      <small class="type">String</small>
    </header>
    <p>
      Name of region.
    </p>
  </li>
</ul>

<h3>2.2 Authoritative Regions</h3>
<ul class="authoritative-regions parameters separator no-style">
  <li id="authoritative-name">
    <header>
      <code>Name</code>
      <small class="type">String</small>
    </header>
    <p>
      Name of place.
    </p>
  </li>
  <li id="authoritative-network">
    <header>
      <code>Network</code>
      <small class="type">String</small>
    </header>
    <p>
      Name of network.
    </p>
  </li>
</ul>

<h3>2.3 FE Regions</h3>
<ul class="fe-regions parameters separator no-style">
  <li id="fe-num">
    <header>
      <code>Num</code>
      <small class="type">Integer</small>
    </header>
    <p>
      FE region identification number.
    </p>
  </li>
  <li id="fe-name">
    <header>
      <code>Name</code>
      <small class="type">String</small>
    </header>
    <p>
      Name of FE region.
    </p>
  </li>
</ul>

<h3>2.4 NEIC Catalog Regions</h3>
<ul class="neiccatalog-regions parameters separator no-style">
  <li id="neiccatalog-name">
    <header>
      <code>Name</code>
      <small>String</small>
    </header>
    <p>
      NEIC catalog name.
    </p>
  </li>
  <li id="neiccatalog-magnitude">
    <header>
      <code>Magnitude</code>
      <small>Decimal</small>
    </header>
    <p>
      Magnitude assocaiated with the place.
    </p>
  </li>
</ul>

<h3>2.5 NEIC response Regions</h3>
<ul class="neicresponse-regions parameters separator no-style">
  <li id="neicresponse-name">
    <header>
      <code>Name</code>
      <small>String</small>
    </header>
    <p>
      NEIC response name.
    </p>
  </li>
  <li id="neicresponse-magnitude">
    <header>
      <code>Magnitude</code>
      <small>Decimal</small>
    </header>
    <p>
      Magnitude assocaiated with the place.
    </p>
  </li>
</ul>

<h2>3. Example</h2>
<h3>3.1 Requests</h3>
<ul>
  <li>
    <p>Region search at latitude 39.5, longitude -105</p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/regions?latitude=39.5&longitude=-105';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
  <li>
    <p>
      Region search at latitude 39.5, longitude -105, includeGeometry set to true.
    </p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/regions?latitude=39.5&longitude=-105&includeGeometry=true';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
  <li>
    <p>
      Region search at latitude 39.5, longitude -105, type set to fe.
    </p>
    <?php
      $url = $HOST_URL_PREFIX . $MOUNT_PATH .
          '/regions?latitude=39.5&longitude=-105&type=fe';
      echo '<a href="', $url, '">', $url, '</a>';
    ?>
  </li>
</ul>

<h3>3.2 Responses</h3>
<div class="row">
  <div class="column one-of-two">
    <h4>3.2.1 Generic Response</h4>
<pre><code>{
  metadata: {
    request: "&lt;web service request URL&gt;",
    submitted: "&lt;ISO 8601 Timestamp&gt;",
    types: [
      &lt;regions type&gt;, ...
    ],
    version: &lt;web service version number&gt;
  },
  admin: {
    type: "FeatureCollection",
    count: &lt;count&gt;,
    features: [
      {
        type: "Feature",
        id: &lt;id&gt;,
        geometry: {
          type: "Polygon",
          coordinates:[
            &lt;longitude&gt;,
            &lt;latitude&gt;
          ]
        }
        properties: {
          &lt;feature properties&gt;,
          ...
        }
      }
    ]
  },
  ...
}
</code></pre>
  </div>
  <div class="column one-of-two">
    <h4>3.2.2 Sample Response</h4>
<pre><code>{
  metadata: {
    request: "/ws/geoserve/regions?latitude=39.5&amp;longitude=-105&amp;type=admin",
    submitted: "2015-07-21T14:38:06+00:00",
    types: [
      "admin"
    ],
    version: "0.0.1"
  },
  admin: {
    type: "FeatureCollection",
    count: 1,
    features: [
      {
        type: "Feature",
        id: 466,
        geometry: null,
        properties: {
          iso: "USA",
          country: "United States",
          region: "Colorado"
        }
      }
    ]
  }
}
</code></pre>
  </div>
</div>
