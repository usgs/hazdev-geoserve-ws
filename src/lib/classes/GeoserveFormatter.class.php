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


  protected function cast ($value, $type) {
    if ($type === 'integer') {
      $value = $this->safeIntval($value);
    } else if ($type === 'float') {
      $value = $this->safeFloatval($value);
    }

    return $value;
  }

  protected function safeIntval ($value) {
    if ($value === null) {
      return null;
    }

    return intval($value);
  }

  protected function safeFloatval ($value) {
    if ($value === null) {
      return null;
    }

    return floatval($value);
  }
}
