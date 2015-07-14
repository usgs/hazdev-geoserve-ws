<?php

include_once $CLASSES_DIR . '/GeoserveCallback.class.php';


/**
 * A callback to stream places from a PlacesFactory.
 */
class RegionsCallback extends GeoserveCallback {

  /**
   * Called for each place found by the index.
   *
   * @param $item an associative array of place properties.
   */
  public function onItem ($item) {
    $properties = array();
    $shape = null;
    $id = null;

    foreach ($item as $key => $value) {
      if ($key === 'id') {
        $id = $value;
      } else if ($key === 'shape') {
        $shape = $value;
      } else {
        $properties[$key] = $value;
      }
    }

    $feature = array(
      'type' => 'Feature',
      'id' => $id,
      'geometry' => null,
      'properties' => $properties
    );

    if ($this->count > 0) {
      echo ',';
    }
    $feature = json_encode($feature);
    if ($shape !== null) {
      $feature = str_replace('"geometry":null',
          '"geometry":' . $this->getGeometry($shape),
          $feature);
    }
    echo $feature;
    $this->count++;
  }

  public function getGeometry ($shape) {
    if ($shape === null) {
      return 'null';
    }

    preg_match('/([^\(]+)\((.*)\)/', $shape, $matches);
    $type = $matches[1];
    $coords = $matches[2];
    $coords = strtr($coords, array(
      '(' => '[[',
      ')' => ']]',
      ',' => '],[',
      ' ' => ','
    ));
    $json = '{' .
        '"type":"' . ($type === 'POLYGON' ? 'Polygon' : 'MultiPolygon') . '"' .
        ',"coordinates":[' . $coords . ']' .
        '}';
    return $json;
  }
}
