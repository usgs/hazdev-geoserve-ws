<?php

/**
 * A callback to stream places from a GeoserveFactory.
 */
class GeoserveCallback {

  protected $count;
  protected $starttime;

  /**
   * Construct a new GeoserveCallback.
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
    header('Content-type: application/json; charset=UTF-8');
    echo '{',
        '"type": "FeatureCollection"',
        ',"features": [';
  }

  public function onItem ($item, $index) {
  }

  /**
   * Called after the last onItem call.
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
