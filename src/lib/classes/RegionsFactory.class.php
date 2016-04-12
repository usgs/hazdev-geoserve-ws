<?php

class RegionsFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array(
    'admin',
    'authoritative',
    'fe',
    'neiccatalog',
    'neicresponse',
    'offshore',
    'tectonic',
    'timezone'
  );

  /**
   * Get regions containing point.
   *
   * @param $query {RegionsQuery}
   *        query object
   * @return object of regions keyed by type
   * @throws Exception
   */
  public function get ($query) {
    $data = array();

    if (in_array('admin', $query->type)) {
      $data['admin'] = $this->getAdmin($query);
    }
    if (in_array('authoritative', $query->type)) {
      $data['authoritative'] = $this->getAuthoritative($query);
    }
    if (in_array('fe', $query->type)) {
      $data['fe'] = $this->getFE($query);
    }
    if (in_array('neiccatalog', $query->type)) {
      $data['neiccatalog'] = $this->getNEICCatalog($query);
    }
    if (in_array('neicresponse', $query->type)) {
      $data['neicresponse'] = $this->getNEICResponse($query);
    }
    if (in_array('offshore', $query->type)) {
      $data['offshore'] = $this->getOffshore($query);
    }
    if (in_array('tectonic', $query->type)) {
      $data['tectonic'] = $this->getTectonicSummary($query);
    }
    if (in_array('timezone', $query->type)) {
      $data['timezone'] = $this->getTimezone($query);
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
  public function getAdmin ($query) {
    //Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
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
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, admin' .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }

  /**
  * Get Authoritative Regions
  *
  * @param $query {RegionsQuery}
  * query object
  */
  public function getAuthoritative ($query) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
      ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
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
        ', type' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, authoritative' .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY priority ASC';

    return $this->execute($sql, $params);
  }

  /**
   * Get FE Regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getFE ($query) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
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
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, fe_view' .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY priority ASC, ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }

  /**
   * Get NEIC Regions
   *
   * @param $query {RegionsQuery}
   *        query object
   * @param $table {String}
   *        'neic_catalog' or 'neic_response'
   */
  private function getNEIC ($query, $table) {
    if ($table !== 'neic_catalog' && $table !== 'neic_response') {
      throw new Exception('Type must be "neic_response" or "neic_catalog"');
    }

    //Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
      ':latitude' => $query->latitude,
      ':longitude' => $query->longitude
    );

    $sql .= ' SELECT' .
        ' name as name' .
        ', magnitude as magnitude' .
        ', type as type' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, ' . $table .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }

  /**
   * Get NEIC catalog Regions
   * calls getNEIC
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getNEICCatalog ($query) {
    return $this->getNEIC($query, 'neic_catalog');
  }

  /**
   * Get NEIC Response Regions
   * calls getNEIC
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getNEICResponse ($query) {
    return $this->getNEIC($query, 'neic_response');
  }

  /**
   * Get Offshore Regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getOffshore ($query) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
        ':latitude' => $query->latitude,
        ':longitude' => $query->longitude);

    $sql .= ' SELECT' .
        ' name' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, offshore' .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }

  /**
   * Get tectonic summary regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getTectonicSummary ($query) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
        ':latitude' => $query->latitude,
        ':longitude' => $query->longitude);

    $sql .= ' SELECT' .
        ' name as name' .
        ', summary as summary' .
        ', type as type' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, tectonic_summary' .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }

  /**
   * Get Timezone Region
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getTimezone ($query) {
    //Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geometry' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
      ':latitude' => $query->latitude,
      ':longitude' => $query->longitude
    );

    $sql .= ' SELECT' .
        ' timezone as timezone' .
        ', dststart as dststart' .
        ', dstend as dstend' .
        ', standardoffset as standardoffset' .
        ', dstoffset as dstoffset' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsGeoJSON(shape) as shape';
    }

    $sql .= ' FROM search, timezone' .
        ' WHERE search.point && shape' .
        ' AND ST_Intersects(search.point, shape)' .
        ' ORDER BY ST_Area(shape) ASC';

    return $this->execute($sql, $params);
  }


  public function getCasts ($type) {
    if ($type === 'fe') {
      return array(
        'number' => 'integer'
      );
    } else if ($type === 'neiccatalog' || $type === 'neicresponse') {
      return array(
        'magnitude' => 'float'
      );
    }
  }

}
