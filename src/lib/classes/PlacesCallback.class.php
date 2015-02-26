<?php

/**
 * A callback to stream places from a GeoserveFactory.
 */
class PlacesCallback {

  protected $count;
  protected $starttime;

  /**
   * Construct a new PlacesCallback.
   */
  public function __construct() {
    $this->count = 0;
    $this->starttime = time();
  }

  /**
   * Called when there is a database error.
   */
  public function onError($errorInfo) {
    // log locally
    trigger_error($errorInfo[0] . ' (' . $errorInfo[1] . '):' . $errorInfo[2]);

    // sanitize error
    throw new Exception('database error logged on server');
  }

  /**
   * Called when a query is successful, before the first onPlace call.
   *
   * @param $query the query that executed and is about to generate events.
   */
  public function onStart($query) {
    header('Content-type: application/json');
    echo '{',
        '"type": "FeatureCollection"',
        ',"features": [';
  }

  /**
   * Called for each place found by the index.
   *
   * @param $place an associative array of place properties.
   * @param $index the FDSNIndex that is executing the query.
   */
  public function onPlace($place, $index) {
    echo ($this->count > 0 ? ',' : ''),
        '{',
          '"type": "Feature",',
          '"id":', $place['geoname_id'], ',',
          '"properties":{',
            '"admin1_code":"', $place['admin1_code'], '",',
            '"admin1_name":"', $place['admin1_name'], '",',
            '"azimuth":', round($place['azimuth'], 1), ',',
            '"country_code":"', $place['country_code'], '",',
            '"country_name":"', $place['country_name'], '",',
            '"distance":', round($place['distance'], 3), ',',
            '"feature_class":"', $place['feature_class'], '",',
            '"feature_code":"', $place['feature_code'], '",',
            '"name":"', $place['name'], '",',
            '"population":', intval($place['population']),
          '},',
          '"geometry":{',
            '"type":"Point",',
            '"coordinates":[',
              floatval($place['longitude']), ',',
              floatval($place['latitude']), ',',
              floatval($place['elevation']),
            ']',
          '}',
        '}';
    $this->count++;
  }

  /**
   * Called after the last onEvent call.
   */
  public function onEnd() {
    global $CONFIG;
    global $HOST_URL_PREFIX;

    echo '],',
        '"metadata": {',
          '"count":', $this->count,
          ',"generated":', $this->starttime, '000',
          ',"status":200',
          ',"url":"', $HOST_URL_PREFIX, $_SERVER['REQUEST_URI'], '"',
          ',"version":"', $CONFIG['GEOSERVE_VERSION'], '"',
        '}',
        '}';
  }

}
