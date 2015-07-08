<?php

abstract class GeoserveFactory {

  protected $db;
  protected $db_dsn;
  protected $db_user;
  protected $db_pass;

  /**
   * Construct a new PlacesFactory.
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
  public abstract function get ($query, $callback);
}
