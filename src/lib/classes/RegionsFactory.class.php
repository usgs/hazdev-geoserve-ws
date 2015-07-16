<?php

class RegionsFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array(
    'admin',
    'authoritative',
    'fe'
  );

  /**
   * Get regions containing point.
   *
   * @param $query {RegionsQuery}
   *        query object
   * @param $callback {RegionsCallback}
   *        callback object.
   *        no longer supported, kept only to conform to GeoserveFactory.
   * @return when callback is not null, nothing
   *         when callback is null:
   *         object of regions keyed by type
   * @throws Exception
   */
  public function get ($query, $callback) {
    $data = array();

    if ($query->type === null || in_array('admin', $query->type)) {
      $data['admin'] = $this->getAdmin($query, $callback);
    }
    if ($query->type === null || in_array('authoritative', $query->type)) {
      $data['authoritative'] = $this->getAuthoritative($query, $callback);
    }
    if ($query->type === null || in_array('fe', $query->type)) {
      $data['fe'] = $this->getFE($query, $callback);
    }

    return $data;
  }

  /**
   * @return {Array}
   *         An array of supported types
   */
  public function getSupportedTypes () {
    return RegionsFactory::$SUPPORTED_TYPES;
  }

  /**
   * Get Admin Regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getAdmin ($query, $callback = null ) {
    //Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
      ':latitude' => $query->latitude,
      ':longitude' => $query->longitude
    );

    $sql .= ' SELECT' .
        ' iso as iso' .
        ', country as country' .
        ', region as region' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsText(shape) as shape';
    }

    $sql .= ' FROM search, admin' .
        ' WHERE search.point && shape' .
        ' ORDER BY ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }

  /**
  * Get Authoritative Regions
  *
  * @param $query {RegionsQuery}
  * query object
  */
  public function getAuthoritative ($query, $callback = null) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
      ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
      ' AS point' .
      ')';
    //bound parameters
    $params = array(
      ':latitude' => $query->latitude,
      ':longitude' => $query->longitude
    );

    $sql .= ' SELECT' .
        ' name as name' .
        ', network as network' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsText(shape) as shape';
    }

    $sql .= ' FROM search, authoritative' .
        ' WHERE search.point && shape' .
        ' ORDER BY priority ASC';

    return $this->execute($sql, $params);
  }

  /**
   * Get FE Regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getFE ($query, $callback = null) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
        ':latitude' => $query->latitude,
        ':longitude' => $query->longitude);

    $sql .= ' SELECT' .
        ' num as number' .
        ', place as name' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsText(shape) as shape';
    }

    $sql .= ' FROM search, fe_view' .
        ' WHERE search.point && shape' .
        ' ORDER BY priority ASC, ST_Area(shape) ASC';

    return $this->execute($sql, $params);
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
      try {
        // return all matching rows
        return $query->fetchAll(PDO::FETCH_ASSOC);
      } finally {
        $query->closeCursor();
      }
    }
  }

}
