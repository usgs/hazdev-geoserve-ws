<?php

include_once $CLASSES_DIR . '/PlacesFormatter.class.php';
include_once $CLASSES_DIR . '/PlacesQuery.class.php';

include_once $CLASSES_DIR . '/RegionsFormatter.class.php';
include_once $CLASSES_DIR . '/RegionsQuery.class.php';


class GeoserveWebService {

  // the GeoserveFactory to use
  public $placesFactory;
  public $regionsFactory;

  // service version number
  public $version;

  const BAD_REQUEST = 400;
  const NOT_FOUND = 404;
  const NOT_IMPLEMENTED = 501;

  // status message text
  public static $statusMessage = array(
    self::BAD_REQUEST => 'Bad Request',
    self::NOT_FOUND => 'Not Found',
    self::NOT_IMPLEMENTED => 'Not Implemented',
  );


  public function __construct($placesFactory, $regionsFactory) {
    $this->placesFactory = $placesFactory;
    $this->regionsFactory = $regionsFactory;

    global $CONFIG;
    $this->version = $CONFIG['GEOSERVE_VERSION'];
  }


  public function places ($params) {
    $placesFormatter = new PlacesFormatter();
    $placesQuery = $this->parsePlacesQuery($params);
    $places = $this->placesFactory->get($placesQuery, null);
    $this->output($places, $placesFormatter);
  }

  public function regions ($params) {
    $regionsFormatter = new RegionsFormatter();
    $regionsQuery = $this->parseRegionsQuery($params);
    $regions = $this->regionsFactory->get($regionsQuery, null);
    $this->output($regions, $regionsFormatter);
  }

  public function output ($data, $formatter) {
    global $APP_DIR;

    $CACHE_MAXAGE = 3600;
    include $APP_DIR . '/lib/cache.inc.php';

    header('Content-Type: application/json');
    echo '{';

    // service/request metadata
    echo '"metadata":' .
        json_encode(array(
          'request' => $_SERVER['REQUEST_URI'],
          'submitted' => gmdate('c'),
          'types' => array_keys($data),
          'version' => $this->version
        ));

    foreach ($data as $type => $items) {
      $formatted = array();
      foreach ($items as $item) {
        $formatted[] = $formatter->formatItem($item, $type);
      }

      // each type is a separate feature collection
      echo ',"' . $type . '":' .
          '{' .
            '"type":"FeatureCollection",' .
            '"count":' . count($items) . ',' .
            '"features":[' .
              implode(',', $formatted) .
            ']' .
          '}';
    }

    echo '}';

    exit();
  }

  public function error ($code, $message) {
    global $APP_DIR;

    // only cache errors for 60 seconds
    $CACHE_MAXAGE = 60;
    include $APP_DIR . '/lib/cache.inc.php';

    if (isset(self::$statusMessage[$code])) {
      $codeMessage = self::$statusMessage[$code];
    } else {
      $codeMessage = '';
    }

    header('HTTP/1.0 ' . $code .
        ($codeMessage !== '' ? ' ' : '') . $codeMessage);
    if ($code < 400) {
      exit();
    }

    global $HOST_URL_PREFIX;
    global $MOUNT_PATH;

    header('Content-type: application/json');
    echo json_encode(array(
      'metadata' => array(
        'request' => $_SERVER['REQUEST_URI'],
        'submitted' => gmdate('c'),
        'types' => array(),
        'version' => $this->version
      ),
      'error' => array(
        'code' => $code,
        'codeMessage' => $codeMessage,
        'message' => $message,
        'usage' => $HOST_URL_PREFIX . $MOUNT_PATH,
      )
    ));

    exit();
  }


  public function parsePlacesQuery ($params) {
    $query = new PlacesQuery();
    $query->type = array();
    $circleSearch = false;
    $rectangleSearch = false;

    foreach ($params as $name => $value) {
      if ($value === '') {
        // check for empty values in non-javascript
        continue;
      } else if ($name === 'method') {
        // used by apache rewrites
        continue;
      } else if ($name ==='latitude' || $name ==='lat') {
        $circleSearch = true;
        $query->latitude = $this->validateFloat($name, $value, -90, 90);
      } else if ($name === 'minlatitude') {
        $rectangleSearch = true;
        $query->minlatitude = $this->validateFloat($name, $value, -90, 90);
      } else if ($name === 'maxlatitude') {
        $rectangleSearch = true;
        $query->maxlatitude = $this->validateFloat($name, $value, -90, 90);
      } else if ($name ==='longitude' || $name ==='lon') {
        $circleSearch = true;
        $query->longitude = $this->validateFloat($name, $value, -180, 180);
      } else if ($name ==='minlongitude') {
        $rectangleSearch = true;
        $query->minlongitude = $this->validateFloat($name, $value, -360, 360);
      } else if ($name ==='maxlongitude') {
        $rectangleSearch = true;
        $query->maxlongitude = $this->validateFloat($name, $value, -360, 360);
      } else if ($name==='maxradiuskm') {
        $circleSearch = true;
        $query->maxradiuskm = $this->validateFloat($name, $value, 0, 20001.6);
      } else if ($name ==='minpopulation') {
        $query->minpopulation = $this->validateInteger($name, $value, 0, null);
      } else if ($name ==='limit') {
        $query->limit = $this->validateInteger($name, $value, 1, null);
      } else if ($name === 'type') {
        $supportedTypes = $this->placesFactory->getSupportedTypes();
        $types = explode(',', $value);

        foreach ($types as $type) {
          $query->type[] = $this->validateEnumerated(
              $name, $type, $supportedTypes);
        }
      } else {
        $this->error(self::BAD_REQUEST,
            'Unknown parameter "' . $name . '".');
      }
    }

    if ($circleSearch && $rectangleSearch) {
      $this->error(self::BAD_REQUEST,
          'can not search by both circles and rectangles');
    }

    if ($circleSearch &&
        ($query->latitude === null || $query->longitude === null)) {
      $this->error(self::BAD_REQUEST,
          'latitude and longitude are required for circle searches');
    }

    if ($rectangleSearch &&
        ($query->minlatitude === null || $query->maxlatitude === null ||
        $query->minlongitude === null || $query->maxlongitude === null)) {
      $this->error(self::BAD_REQUEST,
          'min/max latitude/longitude are required for rectangle searches');
    }

    if ($circleSearch && !in_array('event', $query->type) &&
        $query->maxradiuskm === null && $query->limit === null) {
      $this->error(self::BAD_REQUEST,
          'maxradiuskm or limit is required for circle searches');
    }

    if ($circleSearch === null) {
      if (in_array('event', $query->type)) {
        $this->error(self::BAD_REQUEST,
            'latitude and longitude are required');
      } else if (in_array('geonames', $query->type) && $rectangleSearch === null) {
        $this->error(self::BAD_REQUEST,
            'latitude/longitude OR min/max latitude/longitude are required');
      }
    }


    return $query;
  }

  public function parseRegionsQuery ($params) {
    $query = new RegionsQuery();

    foreach ($params as $name => $value) {
      if ($values === '') {
        continue;
      } else if ($name === 'method') {
        continue;
      } else if ($name === 'latitude') {
        $query->latitude = $this->validateFloat($name, $value, -90, 90);
      } else if ($name === 'longitude') {
        $query->longitude = $this->validateFloat($name, $value, -180, 180);
      } else if ($name === 'includeGeometry') {
        $query->includeGeometry = $this->validateBoolean($name, $value);
      } else if ($name === 'type') {
        $supportedTypes = $this->regionsFactory->getSupportedTypes();
        $types = explode(',', $value);
        $query->type = array();

        foreach ($types as $type) {
          $query->type[] = $this->validateEnumerated(
              $name, $type, $supportedTypes);
        }
      } else {
        $this->error(self::BAD_REQUEST,
            'Unknown parameter "' . $name . '".');
      }
    }

    if ($query->latitude === null || $query->longitude === null) {
      $this->error(self::BAD_REQUEST,
          'latitude and longitude are required');
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
  protected function validateBoolean ($param, $value) {
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
  protected function validateInteger ($param, $value, $min, $max) {
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
  protected function validateFloat ($param, $value, $min, $max) {
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
  protected function validateEnumerated ($param, $value, $enum) {
    if (!in_array($value, $enum)) {
      $this->error(self::BAD_REQUEST, 'Bad ' . $param .
        ' value "' . $value . '".' .
        ' Valid values are: "' . implode('", "', $enum) . '".');
    }
    return $value;
  }

}
