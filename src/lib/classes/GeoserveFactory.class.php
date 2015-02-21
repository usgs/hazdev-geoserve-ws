<?php

class GeoserveFactory {

  // default optoins for getPlaces()
  public static $GET_PLACES_DEFAULTS = array(
    // restrict types of places returned.
    'featureCode' => null,
    // maximum number of places to return
    'limit' => 5,
    // maximum distance in meters
    'distance' => null,
    // minimum population in people
    'population' => null
  );

  private $db;
  private $db_dsn;
  private $db_user;
  private $db_pass;


  /**
   * Construct a new GeoserveFactory.
   *
   * @param $db_dsn {String}
   *        PDO DSN for database.
   *        Example: 'pgsql:host=localhost;port=5432;dbname=geoserve'.
   * @param $db_user {String}
   *        database username.
   * @param $db_pass {String}
   *        database password.
   */
  public function __construct($db_dsn, $db_user, $db_pass) {
    $this->db = null;
    $this->db_dsn = $db_dsn;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
  }


  /**
   * Create connection for database.
   * Called during first use of factory.
   */
  public function connect() {
    if ($this->db === null) {
      $this->db = new PDO($this->db_dsn, $this->db_user, $this->db_pass);
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $this->db;
  }

  /**
   * Close connection to database.
   */
  public function disconnect() {
    $this->db = null;
  }


  /**
   * Get nearby places.
   *
   * @param $latitude {Number}
   *        latitude in decimal degrees [-90, 90].
   * @param $longitude {Number}
   *        longitude in decimal degrees [-180, 180].
   * @param $options {Array}
   *        array of options.
   * @param $options['featureCode'] {String}
   *        return places with a specific feature code.
   * @param $options['limit'] {Number}
   *        return at most this many places.
   *        default 5.
   *        set to null to remove limit.
   * @param $options['distance'] {Number}
   *        return places closer than this distance in meters.
   *        default null (any distance).
   * @param $options['population'] {Number}
   *        return places with at least this population.
   *        default null (any population).
   * @return array of places, with these additional columns:
   *         "azimuth" - direction from search point to place,
   *                     in degrees clockwise from geographic north.
   *         "distance" - distance in meters
   * @throws Exception
   *         if at least one of $options['limit'] or $options['distance']
   *         is not specified.
   */
  public function getPlaces($latitude, $longitude, $options = null) {
    $options = array_merge(array(), self::$GET_PLACES_DEFAULTS,
        ($options === null ? array() : $options));

    if ($options['limit'] === null && $options['distance'] === null) {
      throw new Exception('"limit" and/or "distance" is required');
    }

    // connect to database
    $db = $this->connect();

    // computed values
    $azimuth = 'degrees(ST_Azimuth(' .
          'ST_SetSRID(ST_MakePoint(:longitude,:latitude), 4326)::geography' .
          ',shape))';
    $distance = 'ST_Distance(' .
          'ST_SetSRID(ST_MakePoint(:longitude,:latitude), 4326)::geography' .
          ',shape)';
    // bound parameters
    $params = array(
        ':latitude' => $latitude,
        ':longitude' => $longitude);

    // create sql
    $sql =  'SELECT *' .
        ',' . $azimuth . ' as azimuth' .
        ',' .$distance . ' as distance' .
        ' FROM geoname';
    // build where clause
    $where = array();
    if ($options['distance'] !== null) {
      $where[] = $distance . ' <= :distance';
      $params[':distance'] = $options['distance'];
    }
    if ($options['population'] !== null) {
      $where[] = 'population >= :population';
      $params[':population'] = $options['population'];
    }
    if ($options['featureCode'] !== null) {
      $where[] = 'feature_code = :feature_code';
      $params[':feature_code'] = $options['featureCode'];
    }
    if (count($where) > 0) {
      $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    // sort closest places first
    $sql .= ' ORDER BY ' . $distance;
    // limit number of results
    if ($options['limit'] !== null) {
      $sql .= ' LIMIT :limit';
      $params[':limit'] = $options['limit'];
    }

    // execute query
    try {
      $query = $db->prepare($sql);
      if (!$query->execute($params)) {
        $errorInfo = $db->errorInfo();
        throw new Exception($errorInfo[0] . ' (' . $errorInfo[1] . ') ' . $errorInfo[2]);
      }
      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      trigger_error($e->getMessage());
      return null;
    } finally {
      // close handle
      $query = null;
    }
  }

}
