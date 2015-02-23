<?php

if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';

  $TITLE = "Nearby Places";
  include_once 'template.inc.php';
}

$latitude = param('latitude', null);
$longitude = param('longitude', null);
$limit = param('limit');
$distance = param('distance');
$population = param('population');
?>

<form method="get" action="index.php" class="vertical">
  <label for="latitude">Latitude [-90, 90]</label>
  <input id="latitude" name="latitude" type="number"<?php
    if ($latitude !== null) {
      echo ' value="' . htmlentities($latitude) . '"';
    }
  ?> min="-90" max="90" step="0.001" required="required"/>

  <label for="longitude">Longitude [-180, 180]</label>
  <input id="longitude" name="longitude" type="number"<?php
    if ($longitude !== null) {
      echo ' value="' . htmlentities($longitude) . '"';
    }
  ?> min="-180" max="180" step="0.001" required="required"/>

  <label for="limit">Limit</label>
  <input id="limit" name="limit" type="number" min="0"<?php
    if ($limit !== null) {
      echo ' value="' . htmlentities($limit) . '"';
    }
  ?>/>

  <label for="distance">Max Distance (km)</label>
  <input id="distance" name="distance" type="number"<?php
    if ($distance !== null) {
      echo ' value="' . htmlentities($distance) . '"';
    }
  ?> min="0" step="0.001"/>

  <label for="population">Min Population</label>
  <input id="population" name="population" type="number"<?php
    if ($population !== null) {
      echo ' value="' . htmlentities($population) . '"';
    }
  ?> min="0"/>

  <div>
    <button>Search</button>
  </div>
</form>

<?php

if ($latitude !== null && $longitude !== null) {
  $options = array(
    'distance' => ($distance === '' ? null : $distance * 1000),
    'limit' => ($limit === '' ? null : $limit),
    'population' => ($population === '' ? null : $population)
  );
  $nearby = $FACTORY->getPlaces($latitude, $longitude, $options);

  echo '<p>' . count($nearby) . ' matching places.</p>';
  if (count($nearby) === 0) {
    return;
  }

  echo '<table class="tabular">' .
      '<thead><tr>' .
        '<th>Place</th>' .
        '<th>Distance</th>' .
        '<th>Azimuth</th>' .
        '<th>Population</th>' .
        '<th>Lat, Lon</th>' .
      '</tr></thead>' .
      '<tbody>';

  foreach ($nearby as $place) {
    echo '<tr>' .
        '<td>' . $place['name'] . ', ' . $place['admin1_code'] . '</td>' .
        '<td>' . round($place['distance']/1000, 1) . ' km</td>' .
        '<td>' . round($place['azimuth']) . '&deg;</td>' .
        '<td>' . $place['population'] . '</td>' .
        '<td>' . $place['latitude'] . ', ' . $place['longitude'] . '</td>' .
        '</tr>';
  }

  echo '</tbody>' .
      '</table>';
}

?>
