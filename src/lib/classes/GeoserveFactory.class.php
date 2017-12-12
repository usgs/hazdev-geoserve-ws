<?php

abstract class GeoserveFactory {

  protected $db;
  protected $db_dsn;
  protected $db_user;
  protected $db_pass;
  protected $db_schema;

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
  public function __construct($db_dsn, $db_user, $db_pass, $db_schema = null) {
    $this->db = null;
    $this->db_dsn = $db_dsn;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_schema = $db_schema;
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

    if ($this->db_schema !== null && $this->db_schema !== '') {
      $this->db->exec('SET search_path = ' . $this->db_schema . ', public');
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
   * Execute and return associative array of data.
   *
   * @param $sql {String}
   *        SQL to execute, with named or anonymous parameter placeholders.
   * @param $params {Array}
   *        parameter values.
   * @return {Array<Array>}
   *         array containing one array per row.
   * @throws {Exception} if errors occur.
   */
  protected function execute($sql, $params) {
    $db = $this->connect();
    $query = $db->prepare($sql);
    if (!$query->execute($params)) {
      // handle error
      $errorInfo = $db->errorInfo();
      throw new Exception($errorInfo[2]);
    } else {
      $data = null;
      $exception = null;
      try {
        // return all matching rows
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        $exception = $e;
      }
      $query->closeCursor();
      if ($exception != null) {
        throw $exception;
      }
      return $data;
    }
  }

  /**
   * Get nearby places.
   *
   * @param $query {PlacesQuery}
   *        query object.
   * @return array of features
   * @throws Exception
   *         if at least one of $query->limit or $query->maxradiuskm
   *         is not specified.
   */
  public abstract function get ($query);

  /**
   *
   * @param $type {String}
   *      The type for which to get fields to cast.
   * @return {Array}
   *      An associative array keyed by fields to be cast with corresponding
   *      values identifying the type to cast the field to. Current cast types
   *      include "integer" and "float".
   */
  public abstract function getCasts ($type);

}
