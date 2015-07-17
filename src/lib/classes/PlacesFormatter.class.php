<?php

include_once $CLASSES_DIR . '/GeoserveFormatter.class.php';


/**
 * Format places for geoserve web service.
 */
class PlacesFormatter extends GeoserveFormatter {

  /**
   * Called for each place found by the index.
   *
   * @param $item an associative array of place properties.
   * @param $index the FDSNIndex that is executing the query.
   */
  public function formatItem ($item, $type) {

    $feature = json_encode(array(
      'type' => 'Feature',
      'id' => $item['geoname_id'],
      'geometry' => array(
        'type' => 'Point',
        'coordinates' => [
          floatval($item['longitude']),
          floatval($item['latitude']),
          floatval($item['elevation'])
        ]
      ),
      'properties' => array(
        'admin1_code' => $item['admin1_code'],
        'admin1_name' => $item['admin1_name'],
        'azimuth' => $this->_round($item['azimuth'], 1),
        'country_code' => $item['country_code'],
        'country_name' => $item['country_name'],
        'distance' => $this->_round($item['distance'], 3),
        'feature_class' => $item['feature_class'],
        'feature_code' => $item['feature_code'],
        'name' => $item['name'],
        'population' => intval($item['population'])
      )
    ));

    return $feature;
  }

  protected function _round ($number, $decimals) {
    if ($number === null) {
      return 'null';
    }

    return round($number, $decimals);
  }

}
