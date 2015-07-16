<?php

include_once $CLASSES_DIR . '/GeoserveFormatter.class.php';


/**
 * Format regions for geoserve web service.
 */
class RegionsFormatter extends GeoserveFormatter {

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
   * @param $type {String}
   *        type of region.
   *        currently unused.
   */
  public function formatItem ($item, $type) {
    $id = null;
    $properties = array();
    $shape = null;

    foreach ($item as $key => $value) {
      if ($key === 'id') {
        $id = $value;
      } else if ($key === 'shape') {
        $shape = $value;
      } else {
        $properties[$key] = $value;
      }
    }

    $feature = json_encode(array(
      'type' => 'Feature',
      'id' => $id,
      'geometry' => null,
      'properties' => $properties
    ));

    if ($shape !== null) {
      $feature = str_replace('"geometry":null',
          '"geometry":' . $this->getGeometry($shape),
          $feature);
    }

    return $feature;
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
