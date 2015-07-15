<?php

include_once $CLASSES_DIR . '/GeoserveFactory.class.php';


class PlacesFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array('event', 'geonames');

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
  public function get ($query, $callback=null) {
    if ($callback !== null) {
      $callback->onStart($query);
    }

    $data = array();

    if ($query->type === null || in_array('event', $query->type)) {
      $data['event'] = $this->getEventPlaces($query, $callback);
    }

    if ($query->type === null || in_array('geonames', $query->type)) {
      // determine circle or rectangle
      if ($query->latitude !== null && $query->longitude !== null &&
          $query->maxradiuskm !== null) {
        $data['geonames'] = $this->getByCircle($query, $callback);
      } else if ($query->minlatitude !== null && $query->maxlatitude !== null &&
          $query->minlongitude !== null && $query->maxlongitude !== null) {
        $data['geonames'] = $this->getByRectangle($query, $callback);
      }
    }

    if ($callback !== null) {
      $callback->onEnd();
    } else {
      return $data;
    }
  }

  /**
   * Get nearby places for circle searches.
   *
   */
  public function getByCircle ($query, $callback = null) {
    if ($query->latitude === null || $query->longitude === null ||
        $query->maxradiuskm === null) {
      throw new Exception('"latitude", "longitude", and "maxradiuskm"' .
          ' are required');
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

    $this->_buildGenericWhere($query, $where, $params);

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
    return $this->_execute($sql, $params, $callback);
  }

  /**
   * Get places for rectangle searches.
   *
   * Note :: Unlike with cicles, there is no azimuth or distance generated
   * since there is no "center" of interest per-se.
   *
   */
  public function getByRectangle ($query, $callback = null) {
    if ($query->minlatitude === null || $query->maxlatitude === null ||
        $query->minlongitude === null || $query->maxlongitude === null) {
      throw new Exception('"minlatitude", "maxlatitude", "minlongitude", ',
          '"maxlongitude" are required parameters');
    }

    $sql = '
    SELECT
      geoname.*,
      admin1_codes_ascii.name AS admin1_name,
      country_info.country AS country_name,
      NULL::DECIMAL AS azimuth,
      NULL::DECIMAL AS distance
    FROM
      geoname
    JOIN
      admin1_codes_ascii ON (geoname.country_code || \'.\' || ' .
          'geoname.admin1_code = admin1_codes_ascii.code)
    JOIN
      country_info ON (geoname.country_code = country_info.iso)
    WHERE
    ';

    $where = array();
    $params = array(
      'minlatitude' => $query->minlatitude,
      'maxlatitude' => $query->maxlatitude,
      'minlongitude' => $this->_normalizeLongitude($query->minlongitude),
      'maxlongitude' => $this->_normalizeLongitude($query->maxlongitude)
    );

    if ($params['minlongitude'] > $params['maxlongitude']) {
      // crosses dateline, make two boxes
      $sql .= '((
          geoname.shape &&
          ST_MakeEnvelope(:minlongitude, :minlatitude, 180, :maxlatitude, 4326)
        ) OR (
          geoname.shape &&
          ST_MakeEnvelope(-180, :minlatitude, :maxlongitude, :maxlatitude, 4326)
      ))';
    } else {
      $sql .= '(geoname.shape && ST_MakeEnvelope(' .
          ':minlongitude, :minlatitude, :maxlongitude, :maxlatitude, 4326))';
    }

    $this->_buildGenericWhere($query, $where, $params);

    if (count($where) > 0) {
      $sql .= ' AND ' . implode(' AND ', $where);
    }

    // sort populous places first
    $sql .= ' ORDER BY geoname.population DESC';

    // limit number of results
    if ($query->limit !== null) {
      $sql .= ' LIMIT :limit';
      $params[':limit'] = $query->limit;
    }

    // execute query
    return $this->_execute($sql, $params, $callback);
  }

  /**
   * Get old event page places (five total)
   */
  public function getEventPlaces ($query, $callback = null) {
    // array of places
    $eventplaces = array();
    $results = array();

    /*** Find the closest populated place ***/
    $query->maxradiuskm = 500;
    $query->limit = 1;
    $results = $this->_expandSearch($query);
    $eventplaces = array_merge($eventplaces, $results);

    /*** Find five populated places with population > 10,000 ***/
    $query->limit = 5;
    $query->minpopulation = 10000;
    $results = $this->_expandSearch($query);
    $eventplaces = array_merge($eventplaces, $results);

    /*** remove potential duplicates ***/
    $eventplaces = $this->_removeDuplicates($eventplaces);

    /*** Add capital city ***/
    $capital = array();
    if ($this->_hasCapital($eventplaces) === false) {
      $query->limit = 1;
      $query->minpopulation = null;
      $query->featurecode = 'PPLA';
      $capital = $this->_expandSearch($query);
    }

    /*** limit to 5, make sure capital is in top 5 ***/
    $hasCapital = false;

    for ($i = 0; $i < 5; $i++) {
      // check for capital
      if ($eventplaces[$i]['feature_code'] === 'PPLA' ||
          $eventplaces[$i]['feature_code'] === 'PPLC') {
        $hasCapital = true;
      }
    }

    // build array with 5 places (including capital)
    if ($hasCapital === true) {
      $eventplaces = array_slice($eventplaces, 0, 5);
    } else {
      $eventplaces = array_slice($eventplaces, 0, 4);
      $eventplaces = array_merge($eventplaces, $capital);
    }

    /*** output geojson ***/
    if ($callback !== null) {
      // use callback
      $callback->onTypeStart('event');
      for ($i = 0; $i < count($eventplaces); $i++) {
        $callback->onItem($eventplaces[$i], $this);
      }
      $callback->onTypeEnd();
    } else {
      // return all places
      return $eventplaces;
    }
  }

  /**
   * @return {Array}
   *         An array of supported types
   */
  public function getSupportedTypes () {
    return PlacesFactory::$SUPPORTED_TYPES;
  }

  /**
   * Generic method for parsing "WHERE" parameters that are common to both
   * circle and rectangle searches.
   *
   */
  private function _buildGenericWhere ($query, &$where, &$params) {
    if ($query->minpopulation !== null) {
      $where[] = 'geoname.population >= :minpopulation';
      $params[':minpopulation'] = $query->minpopulation;
    }
    if ($query->featurecode !== null) {
      $where[] = 'geoname.feature_code = :featurecode';
      $params[':featurecode'] = $query->featurecode;
    }
  }

  /**
   * Excutes the query either invoking the callback if provided or returning
   * the result set otherwise.
   *
   */
  private function _execute ($sql, $params, $callback = null) {
    $db = $this->connect();
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
          $callback->onTypeStart('geonames');

          while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $callback->onPlace($row, $this);
          }

          $callback->onTypeEnd();
        } else {
          return $query->fetchAll(PDO::FETCH_ASSOC);
        }
      } finally {
        $query->closeCursor();
      }
    }
  }

  /**
   * expands query->maxradiuskm if $query-> limit has not been satisfied.
   */
  private function _expandSearch($query) {
    $results = array();

    while (count($results) !== $query->limit) {
      $results = $this->getByCircle($query);
      if (count($results) !== $query->limit) {
        // increase search bounds
        $query->maxradiuskm = $query->maxradiuskm * 2;
      }
    }

    return $results;
  }

  /**
   * checks for a capital city in an array of places
   */
  private function _hasCapital($places) {
    for ($i = 0; $i < count($places); $i++) {
      // check for duplicate
      if ($places[$i]['feature_code'] === 'PPLA' ||
          $places[$i]['feature_code'] === 'PPLC') {
        return true;
      }
    }
    return false;
  }

  /**
   * Maps the input longitude into a [-180,.0 +180.0] range.
   *
   */
  private function _normalizeLongitude ($longitude) {
    if ($longitude === null) {
      return null;
    }

    while ($longitude < -180.0) {
      $longitude += 360.0;
    }

    while ($longitude > 180.0) {
      $longitude -= 360.0;
    }

    return $longitude;
  }

  /**
   * removes duplicates from a distance ordered array
   */
  private function _removeDuplicates($places) {
    $previousId = null;
    $duplicateIndex = null;

    for ($i = 0; $i < count($places); $i++) {
      // check for duplicate
      if ($previousId === $places[$i]['geoname_id']) {
        $duplicateIndex = $i;
      }
      $previousId = $places[$i]['geoname_id'];
    }

    if ($duplicateIndex !== null) {
      unset($places[$duplicateIndex]);
      $places = array_values($places);
    }

    return $places;
  }
}
