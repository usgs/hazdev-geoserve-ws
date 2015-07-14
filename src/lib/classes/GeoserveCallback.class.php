<?php

/**
 * A callback to stream places from a GeoserveFactory.
 */
class GeoserveCallback {

  protected $count;
  protected $starttime;
  protected $types;
  protected $query;

  /**
   * Construct a new GeoserveCallback.
   */
  public function __construct() {
    $this->count = 0;
    $this->starttime = time();
    $this->types = array();
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
    $this->query = $query;
    header('Content-type: application/json; charset=UTF-8');
    echo '{';
  }

  public function onTypeStart($name) {
    if (count($this->types) !== 0) {
      echo ',';
    }
    $this->types[] = $name;
    $this->count = 0;
    echo '"' . $name . '":{' .
        '"type": "FeatureCollection"' .
        ',"features": [';
  }

  public function onItem ($item) {
    if ($this->count !== 0) {
      echo ',';
    }

    echo json_encode($item);
  }

  /**
   * Called after the last onItem call.
   */
  public function onEnd() {
    global $CONFIG;
    global $HOST_URL_PREFIX;

    if (count($this->types) !== 0) {
      echo ',';
    }

    $metadata = array(
      'generated' => $this->starttime . '000',
      'status' => 200,
      'url' => $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'],
      'version' => $CONFIG['GEOSERVE_VERSION'],
      'types' => $this->types
    );

    echo '"metadata":' .
        json_encode($metadata) .
        '}';
  }

  public function onTypeEnd() {
    echo '],' .
        '"metadata":{' .
          '"count":' . $this->count .
        '}' .
      '}';
  }
}
