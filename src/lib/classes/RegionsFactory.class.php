<?php

class RegionsFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array();

  /**
   * Get regions containing point.
   *
   * @param $query {RegionsQuery}
   *        query object
   * @param $callback {RegionsCallback}
   *        callback object
   * @return when callback is not null, nothing
   *         when callback is null:
   *         object of regions keyed by type
   * @throws Exception
   */
  public function get ($query, $callback = null) {
    // TODO
  }

  /**
   * @return {Array}
   *         An array of supported types
   */
  public function getSupportedTypes () {
    return RegionsFactory::$SUPPORTED_TYPES;
  }

}
