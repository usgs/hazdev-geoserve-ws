<?php

class GeoserveWebService {

  // the GeoserveFactory to use
  public $factory;

  // service version number
  public $version;

  const BAD_REQUEST = 400;
  const NOT_FOUND = 404;
  const NOT_IMPLEMENTED = 501;
  const SERVICE_UNAVAILABLE = 503;

  // status message text
  public static $statusMessage = array(
    self::BAD_REQUEST => 'Bad Request',
    self::NOT_FOUND => 'Not Found',
    self::NOT_IMPLEMENTED => 'Not Implemented',
    self::SERVICE_UNAVAILABLE => 'Service Unavailable'
  );


  public function __construct($factory) {
    $this->factory = $factory;

    global $CONFIG;
    $this->version = $CONFIG['GEOSERVE_VERSION'];
  }


  public function places() {
    global $APP_DIR;
    global $HOST_URL_PREFIX;

    $query = $this->parsePlacesQuery();
    $places = $this->factory->getPlaces($query);

    // cache results for 1 hour
    $CACHE_MAXAGE = 3600;
    include $APP_DIR . '/lib/cache.inc.php';

    // Does this need to look fully like GeoJSON format?
    $response = array(
      'type' => 'FeatureCollection',
      'metadata' => array(
        'status' => 200,
        'generated' => time() . '000',
        'url' => $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'],
        'version' => $this->version,
        'count' => count($places)
      ),
      'features' => array()
    );

    foreach ($places as $place) {
      $response['features'][] = array(
        'type' => 'Feature',
        'properties' => array(
          'admin1_code' => $place['admin1_code'],
          'azimuth' => round($place['azimuth'], 1),
          'country_code' => $place['country_code'],
          'distance' => round($place['distance'], 1),
          'name' => $place['name'],
          'population' => intval($place['population'])
        ),
        'geometry' => array(
          'type' => 'Point',
          'coordinates' => array(
            floatval($place['longitude']),
            floatval($place['latitude']),
            floatval($place['elevation'])
          )
        )
      );
    }

    $response = preg_replace('/"(generated)":"([\d]+)"/', '"$1":$2',
        str_replace('\/', '/', json_encode($response)));
    if ($query->callback !== null) {
      header('Content-type: text/javascript');
      echo $query->callback, '(', $response, ');';
    } else {
      header('Content-type: application/json');
      echo $response;
    }
  }


  public function error($code, $message, $isDetail = false) {
    global $APP_DIR;

    // only cache errors for 60 seconds
    $CACHE_MAXAGE = 60;
    include $APP_DIR . '/lib/cache.inc.php';

    if (isset(self::$statusMessage[$code])) {
      $codeMessage = ' ' . self::$statusMessage[$code];
    } else {
      $codeMessage = '';
    }

    header('HTTP/1.0 ' . $code . $codeMessage);
    if ($code < 400) {
      exit();
    }

    global $HOST_URL_PREFIX;
    global $MOUNT_PATH;

    // error message for 400 or 500
    header('Content-type: text/plain');
    echo implode("\n", array(
      'Error ' . $code . ': ' . self::$statusMessage[$code],
      '',
      $message,
      '',
      'Usage details are available from ' . $HOST_URL_PREFIX . $MOUNT_PATH,
      '',
      'Request:',
      $_SERVER['REQUEST_URI'],
      '',
      'Request Submitted:',
      gmdate('c'),
      '',
      'Service version:',
      $this->version
    ));
    exit();
  }


  public function parsePlacesQuery() {
    $query = new PlacesQuery();

    $params = $_GET;
    foreach ($params as $name => $value) {
      if ($value === '') {
        // check for empty values in non-javascript
        continue;
      } else if ($name === 'method') {
        // used by apache rewrites
        continue;
      } else if ($name ==='latitude' || $name ==='lat') {
        $query->latitude = $this->validateFloat($name, $value, -90, 90);
      } else if ($name ==='longitude' || $name ==='lon') {
        $query->longitude = $this->validateFloat($name, $value, -180, 180);
      } else if ($name==='maxradiuskm') {
        $query->maxradiuskm = $this->validateFloat($name, $value, 0, 20001.6);
      } else if ($name ==='minpopulation') {
        $query->minpopulation = $this->validateInteger($name, $value, 0, null);
      } else if ($name ==='limit') {
        $query->limit = $this->validateInteger($name, $value, 1, null);
      } else {
        $this->error(self::BAD_REQUEST,
            'Unknown parameter "' . $name . '".');
      }
    }

    if ($query->latitude === null || $query->longitude === null) {
      $this->error(self::BAD_REQUEST,
          'latitude and longitude are required');
    }
    if ($query->maxradiuskm === null && $query->limit === null) {
      $this->error(self::BAD_REQUEST,
          'limit and/or maxradiuskm must be specified');
    }

    return $query;
  }

  /**
   * Validate a boolean parameter.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @return value as boolean if valid ("true" or "false", case insensitively),
   *         exit with error if invalid.
   */
  protected function validateBoolean($param, $value) {
    $val = strtolower($value);
    if ($val !== 'true' && $val !== 'false') {
      $this->error(self::BAD_REQUEST,
          'Bad ' . $param . ' value "' . $value . '".' .
          ' Valid values are (case insensitive): "TRUE", "FALSE".');
    }
    return ($val === 'true');
  }

  /**
   * Validate an integer parameter.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @param $min minimum valid value for parameter, or null if no minimum.
   * @param $max maximum valid value for parameter, or null if no maximum.
   * @return value as integer if valid (integer and in range),
   *         exit with error if invalid.
   */
  protected function validateInteger($param, $value, $min, $max) {
    if (
        !ctype_digit($value)
        || ($min !== null && intval($value) < $min)
        || ($max !== null && intval($value) > $max)
    ) {
      $message = '';
      if ($min === null && $max === null) {
        $message = 'integers';
      } else {
        $message = '';
        if ($min !== null) {
          $message .= $min . ' <= ';
        }
        $message .= $param;
        if ($max !== null) {
          $message .= ' <= ' . $max;
        }
      }
      $this->error(self::BAD_REQUEST, 'Bad ' . $param .
          ' value "' . $value . '".' .
          ' Valid values are ' . $message);
    }
    return intval($value);
  }

  /**
   * Validate a float parameter.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @param $min minimum valid value for parameter, or null if no minimum.
   * @param $max maximum valid value for parameter, or null if no maximum.
   * @return value as float if valid (float and in range),
   *         exit with error if invalid.
   */
  protected function validateFloat($param, $value, $min, $max) {
    if (
        !is_numeric($value)
        || ($min !== null && floatval($value) < $min)
        || ($max !== null && floatval($value) > $max)
    ) {
      if ($min === null && $max === null) {
        $message = 'numeric';
      } else {
        $message = '';
        if ($min !== null) {
          $message .= $min . ' <= ';
        }
        $message .= $param;
        if ($max !== null) {
          $message .= ' <= ' . $max;
        }
      }

      $this->error(self::BAD_REQUEST, 'Bad ' . $param .
          ' value "' . $value . '".' .
          ' Valid values are ' . $mesasge);
    }
    return floatval($value);
  }

  /**
   * Validate a parameter that has an enumerated list of valid values.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @param $enum array of valid parameter values.
   * @return value if valid (in array), exit with error if invalid.
   */
  protected function validateEnumerated($param, $value, $enum) {
    if (!in_array($value, $enum)) {
      $this->error(self::BAD_REQUEST, 'Bad ' . $param .
        ' value "' . $value . '".' .
        ' Valid values are: "' . implode('", "', $enum) . '".');
    }
    return $value;
  }

}