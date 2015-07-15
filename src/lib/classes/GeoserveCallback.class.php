<?php

/**
 * A callback to stream places or regions from a GeoserveFactory.
 *
 * Outputs JSON formatted places or regions.
 *
 * Expected call order:
 * - onStart()
 * for each type of information:
 *    - onTypeStart()
 *    for each item within type:
 *      - onItem()
 *    - onTypeEnd()
 * - onEnd()
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
   * Called when a query is successful, before the first onTypeStart call.
   *
   * @param $query the query that executed and is about to generate events.
   */
  public function onStart($query) {
    $this->query = $query;
    header('Content-type: application/json; charset=UTF-8');
    echo '{';
  }

  /**
  * Start a type feature collection.
  *
  * @param $name type name.
  */
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

  /**
  * Formats one feature within the feature collection.
  *
  * Subclasses should override this method to either make $item a
  * Feature like array, or output a feature for the collection.
  *
  * @param $item {Array}
  *        place or region.
  */
  public function onItem ($item) {
    if ($this->count !== 0) {
      echo ',';
    }

    echo json_encode($item);
  }


  /**
   * End a type feature collection.
   */
  public function onTypeEnd() {
    echo '],' .
        '"metadata":{' .
          '"count":' . $this->count .
        '}' .
      '}';
  }

  /**
   * Called when no more data will be output.
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

}
