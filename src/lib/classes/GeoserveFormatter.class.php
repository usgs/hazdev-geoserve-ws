<?php


/**
 * Format items for geoserve web service.
 */
class GeoserveFormatter {

  /**
   * Format one item.
   *
   * @param $item {Array}
   *        associative array reprensenting one item.
   * @param $type {String}
   *        type of item.
   * @return {String}
   *         JSON formatted item.
   *         Typically a GeoJSON Feature.
   */
  public function formatItem($item, $type) {
    return json_encode($item);
  }

}
