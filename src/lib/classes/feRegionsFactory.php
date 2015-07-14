<?php
class feRegionsFactory extends RegionsFactory {
  public function getfeRegions ($query) {

    $feRegion = array();

    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    } else {
      $lat = $query->latitude;
      $lng = $query->longitude;

      // connects to Data Base
      $db = $this->connect();

      $feRegion[] =
          'SELECT * FROM fe_view WHERE ST_Point('.$lng.','.$lat.') && shape ' .
          'ORDER BY priority ASC, ST_Area(shape)';

      return $feRegion;
    }
  }
}
