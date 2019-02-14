<?php

include_once $CLASSES_DIR . '/RegionsFactory.class.php';

class LayersFactory extends RegionsFactory {

  protected static $SUPPORTED_TYPES = array(
    //'admin',
    'anss',
    'authoritative',
    'fe',
    'neiccatalog',
    'neicresponse',
    //'offshore',
    'tectonic'
    //'timezone'
  );

  protected static $TABLE_NAMES = array(
    // endpoint => table
    // 'admin' => 'admin',
    'anss' => 'authoritative',
    'authoritative' => 'authoritative',
    'fe' => 'fe_view',
    'neiccatalog' => 'neic_catalog',
    'neicresponse' => 'neic_response',
    // 'offshore' => 'offshore',
    'tectonic' => 'tectonic_summary'
    //'timezone' => 'timezone'
  );

  protected static $COLUMN_NAMES = array(
    // 'admin' => array(
    //   'id',
    //   'iso',
    //   'country',
    //   'region',
    //   'ST_AsGeoJSON(shape) AS shape'
    // ),
    'anss' => array(
      'id',
      'name',
      'type',
      'priority',
      'network',
      'region',
      'url',
      'ST_AsGeoJSON(shape) AS shape'
    ),
    'authoritative' => array(
      'id',
      'name',
      'type',
      'priority',
      'network',
      'region',
      'url',
      'ST_AsGeoJSON(shape) AS shape'
    ),
    'fe' => array(
      'id',
      'num',
      'place',
      'dataset',
      'ST_AsGeoJSON(shape) AS shape'
    ),
    'neiccatalog' => array(
      'id',
      'name',
      'magnitude',
      'type',
      'ST_AsGeoJSON(shape) AS shape'
    ),
    'neicresponse' => array(
      'id',
      'name',
      'magnitude',
      'type',
      'ST_AsGeoJSON(shape) AS shape'
    ),
    // 'offshore' => array(
    //   'id',
    //   'name',
    //   'ST_AsGeoJSON(shape) AS shape'
    // ),
    'tectonic' => array(
      'id',
      'name',
      'summary',
      'type',
      'ST_AsGeoJSON(shape) AS shape'
    )
    // 'timezone' => array(
    //   'id',
    //   'timezone',
    //   'dststart',
    //   'dstend',
    //   'standardoffset',
    //   'dstoffset',
    //   'ST_AsGeoJSON(shape) AS shape'
    // )
  );

  protected static $WHERE = array(
    'anss' => 'type = \'anss\''
  );


  public function get ($query) {
    $data = array();

    $sql = '
      SELECT
        ' . implode(', ', $this->getColumnNames($query->type)) . '
      FROM
        ' . $this->getTableName($query->type) . '
      ' . $this->getWhere($query->type);

    $data[$query->type] = $this->execute($sql, array());

    return $data;
  }

  /**
   * @return {Array}
   *         An array of supported types
   */
  public function getSupportedTypes () {
    return LayersFactory::$SUPPORTED_TYPES;
  }

  /**
   * Maps an endpoint name to a table name.
   *
   * @param name {String}
   *      The endpoint name.
   *
   * @return {String}
   *      The table name
   */
  private function getTableName ($name) {
    $table = $name;

    if (isset(LayersFactory::$TABLE_NAMES[$name])) {
      $table = LayersFactory::$TABLE_NAMES[$name];
    }

    return $table;
  }

  private function getColumnNames ($name) {
    $columns = array('*');

    if (isset(LayersFactory::$COLUMN_NAMES[$name])) {
      $columns = LayersFactory::$COLUMN_NAMES[$name];
    }

    return $columns;
  }

  private function getWhere ($name) {
    $where = '';

    if (isset(LayersFactory::$WHERE[$name])) {
      $where = 'WHERE ' . LayersFactory::$WHERE[$name];
    }

    return $where;
  }

}
