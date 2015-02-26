<?php

class GeoserveFactory {

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
   * @param $query {PlacesQuery}
   *        query object.
   * @param $callback {PlacesCallback}
   *        callback object.
   * @return when callback is not null, nothing;
   *         when callback is null:
   *         array of places, with these additional columns:
   *         "azimuth" - direction from search point to place,
   *                     in degrees clockwise from geographic north.
   *         "distance" - distance in meters
   * @throws Exception
   *         if at least one of $query->limit or $query->maxradiuskm
   *         is not specified.
   */
  public function getPlaces($query, $callback=null) {
    if ($query->latitude === null || $query->longitude === null ||
        $query->maxradiuskm === null) {
      throw new Exception('"latitude", "longitude", and "maxradiuskm"' .
          ' are required');
    }

    // connect to database
    $db = $this->connect();

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
        ':latitude' => $query->latitude,
        ':longitude' => $query->longitude);

    // create sql
    $sql .=  ' SELECT' .
        ' geoname.*' .
        ' ,admin1_codes_ascii.name as admin1_name' .
        ' ,country_info.country as country_name' .
        ' ,degrees(ST_Azimuth(search.point, shape)) AS azimuth' .
        ' ,ST_Distance(search.point, shape)/1000 AS distance' .
        ' FROM search, geoname ' .
        ' JOIN admin1_codes_ascii ON (geoname.country_code || \'.\' ||' .
            ' geoname.admin1_code = admin1_codes_ascii.code)'.
        ' JOIN country_info ON (geoname.country_code = country_info.iso)';
    // build where clause
    $where = array();
    if ($query->maxradiuskm !== null) {
      $where[] = 'ST_DWithin(search.point, shape, :distance)';
      $params[':distance'] = $query->maxradiuskm * 1000;
    }
    if ($query->minpopulation !== null) {
      $where[] = 'population >= :minpopulation';
      $params[':minpopulation'] = $query->minpopulation;
    }
    if (count($where) > 0) {
      $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    // sort closest places first
    $sql .= ' ORDER BY distance';
    // limit number of results
    if ($query->limit !== null) {
      $sql .= ' LIMIT :limit';
      $params[':limit'] = $query->limit;
    }

    // execute query
    $query = $db->prepare($sql);
    if (!$query->execute($params)) {
      // handle error
      $errorInfo = $db->errorInfo();
      if ($callback !== null) {
        $callback->onError($errorInfo);
      } else {
        throw new Exception($errorInfo[2]);
      }
    } else {
      try {
        if ($callback !== null) {
          // use callback
          $callback->onStart($query);
          while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $callback->onPlace($row, $this);
          }
          $callback->onEnd();
        } else {
          // return all places
          return $query->fetchAll(PDO::FETCH_ASSOC);
        }
      } finally {
        $query->closeCursor();
      }
    }
  }

}
