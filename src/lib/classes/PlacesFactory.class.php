<?php

include_once $CLASSES_DIR . '/GeoserveFactory.class.php';


class PlacesFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array('event', 'geonames');

  /**
   * Get nearby places.
   *
   * @param $query {PlacesQuery}
   *        query object.
   * @return object of places keyed by type
   * @throws Exception
   */
  public function get ($query) {
    $data = array();

    if ($query->type === null || in_array('event', $query->type)) {
      $data['event'] = $this->getEventPlaces($query);
    }
    if ($query->type === null || in_array('geonames', $query->type)) {
      $data['geonames'] = $this->getGeonames($query);
    }

    return $data;
  }

  /**
   * Get nearby places for circle searches.
   *
   */
  public function getByCircle ($query) {
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
    return $this->execute($sql, $params);
  }

  /**
   * Get places for rectangle searches.
   *
   * Note :: Unlike with cicles, there is no azimuth or distance generated
   * since there is no "center" of interest per-se.
   *
   */
  public function getByRectangle ($query) {
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
    return $this->execute($sql, $params);
  }

  /**
   * Get old event page places (five total)
   */
  public function getEventPlaces ($query) {
    // do not modify $query
    $query = clone $query;

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

    // return all places
    return $eventplaces;
  }

  public function getGeonames ($query) {
    // determine circle or rectangle
    if ($query->latitude !== null && $query->longitude !== null &&
        $query->maxradiuskm !== null) {
      return $this->getByCircle($query);
    } else if ($query->minlatitude !== null && $query->maxlatitude !== null &&
        $query->minlongitude !== null && $query->maxlongitude !== null) {
      return $this->getByRectangle($query);
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
