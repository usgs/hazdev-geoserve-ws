<?php

include_once $CLASSES_DIR . '/GeoserveCallback.class.php';


/**
 * A callback to stream places from a PlacesFactory.
 */
class PlacesCallback extends GeoserveCallback {

  /**
   * @Deprecated
   * @see PlacesCallback#onItem
   */
  public function onPlace($place, $index) {
    return $this->onItem($place, $index);
  }

  /**
   * Called for each place found by the index.
   *
   * @param $item an associative array of place properties.
   * @param $index the FDSNIndex that is executing the query.
   */
  public function onItem ($item, $index) {
    echo ($this->count > 0 ? ',' : ''),
        '{',
          '"type": "Feature",',
          '"id":', $item['geoname_id'], ',',
          '"properties":{',
            '"admin1_code":"', $item['admin1_code'], '",',
            '"admin1_name":"', $item['admin1_name'], '",',
            '"azimuth":', round($item['azimuth'], 1), ',',
            '"country_code":"', $item['country_code'], '",',
            '"country_name":"', $item['country_name'], '",',
            '"distance":', round($item['distance'], 3), ',',
            '"feature_class":"', $item['feature_class'], '",',
            '"feature_code":"', $item['feature_code'], '",',
            '"name":"', $item['name'], '",',
            '"population":', intval($item['population']),
          '},',
          '"geometry":{',
            '"type":"Point",',
            '"coordinates":[',
              floatval($item['longitude']), ',',
              floatval($item['latitude']), ',',
              floatval($item['elevation']),
            ']',
          '}',
        '}';
    $this->count++;
  }

}
