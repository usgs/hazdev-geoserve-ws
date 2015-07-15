<?php

include_once $CLASSES_DIR . '/GeoserveCallback.class.php';


/**
 * A callback to stream regions from RegionsFactory.
 *
 * Outputs JSON formatted region information.
 */
class RegionsCallback extends GeoserveCallback {

  /**
   * Called for each region found by the index.
   *
   * @param $item {Array}
   *        an associative array of region properties.
   *        Except for the following properties, all keys/values are output as
   *        feature properties.
   *
   *        $item['id'] {String|Number}
   *            default null.
   *            output as feature id.
   *        $item['shape'] {String}
   *            default null.
   *            output as feature geometry.
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

  /**
   * Formats shape data to json.
   *
   * Because WKT and GeoJSON geometry information is similar
   * (longitude before latitude), this method uses string replacement and
   * does not interpret any of the actual coordinates.
   *
   * @param $shape {String}
   *        (multi)polygon data in Well-Known-Text (WKT) format.
   * @return {String}
   *         JSON string representing geometry.
   *         "null" if $shape is null, or a formatted geometry.
   */
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
