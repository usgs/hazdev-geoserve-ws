<?php

include_once $CLASSES_DIR . '/GeoserveFactory.class.php';


class PlacesFactory extends GeoserveFactory {

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
  public function get($query, $callback=null) {
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
        ' ,degrees(ST_Azimuth(search.point, geoname.shape)) AS azimuth' .
        ' ,ST_Distance(search.point, geoname.shape) / 1000 AS distance' .
        ' FROM search, geoname ' .
        ' JOIN admin1_codes_ascii ON (geoname.country_code || \'.\' ||' .
            ' geoname.admin1_code = admin1_codes_ascii.code)'.
        ' JOIN country_info ON (geoname.country_code = country_info.iso)';
    // build where clause
    $where = array();
    if ($query->maxradiuskm !== null) {
      $where[] = 'ST_DWithin(search.point, geoname.shape, :distance)';
      $params[':distance'] = $query->maxradiuskm * 1000;
    }
    if ($query->minpopulation !== null) {
      $where[] = 'geoname.population >= :minpopulation';
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

  /**
   * @Deprecated
   * @See PlacesFacgtory#get
   *
   */
  public function getPlaces ($query, $callback = null) {
    return $this->get($query, $callback);
  }

}
