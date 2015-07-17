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

    if (in_array('event', $query->type)) {
      $data['event'] = $this->getEventPlaces($query);
    }
    if (in_array('geonames', $query->type)) {
      $data['geonames'] = $this->getGeonames($query);
    }

    return $data;
  }

  /**
   * Get nearby places for circle searches.
   */
  public function getByCircle ($query) {
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude" and "longitude" are required');
    } else if ($query->maxradiuskm === null && $query->limit === null) {
      throw new Exception('"limit" and/or "maxradiuskm" are required');
    }

    if ($query->maxradiuskm === null) {
      // expand search until at limit, starting at 500km.
      $query = clone $query;
      $query->maxradiuskm = 500;
      $results = $this->getByCircle($query);
      while (
          // not enough results
          count($results) < $query->limit &&
          // window not "too large"
          $query->maxradiuskm < 25000) {
        // double search window
        $query->maxradiuskm = $query->maxradiuskm * 2;
        $results = $this->getByCircle($query);
      }
      // either found limit results, or at search window limit.
      return $results;
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
    $sql .=  '
    SELECT
      geoname.*,
      admin1_codes_ascii.name as admin1_name,
      country_info.country as country_name,
      degrees(ST_Azimuth(search.point, geoname.shape)) AS azimuth,
      ST_Distance(search.point, geoname.shape) / 1000 AS distance
    FROM search, geoname
    JOIN admin1_codes_ascii ON (admin1_codes_ascii.code =
        geoname.country_code || \'.\' || geoname.admin1_code)
    JOIN country_info ON (geoname.country_code = country_info.iso)
    ';

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
    // closest populated place
    $closest = new PlacesQuery();
    $closest->latitude = $query->latitude;
    $closest->longitude = $query->longitude;
    $closest->limit = 1;

    // capital
    $capital = new PlacesQuery();
    $capital->latitude = $query->latitude;
    $capital->longitude = $query->longitude;
    $capital->featurecode = 'PPLA';
    $capital->limit = 1;

    // rest are populated places with population > 10,000
    $populated = new PlacesQuery();
    $populated->latitude = $query->latitude;
    $populated->longitude = $query->longitude;
    $populated->minpopulation = 10000;
    $populated->limit = 5;

    // combine all places
    $places = array_merge(
        $this->getByCircle($closest),
        $this->getByCircle($capital),
        $this->getByCircle($populated));
    // choose first 5 unique (closest and capital always included because first)
    $eventplaces = array();
    foreach ($places as $place) {
      $eventplaces[$place['geoname_id']] = $place;
      if (count($eventplaces) >= 5) {
        break;
      }
    }
    // extract places
    $eventplaces = array_values($eventplaces);
    // sort by distance
    usort($eventplaces, function ($a, $b) {
      $diff = $a['distance'] - $b['distance'];
      if ($diff < 0) {
        return -1;
      } else if ($diff > 0) {
        return 1;
      }
      return 0;
    });

    return $eventplaces;
  }

  public function getGeonames ($query) {
    // determine circle or rectangle
    if ($query->latitude !== null && $query->longitude !== null) {
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
}
