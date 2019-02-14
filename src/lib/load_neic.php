<?php

// ----------------------------------------------------------------------
// NEIC data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating NEIC dataset. Existing data will be deleted, continue?",
    (
      DB_FULL_LOAD ||
      !(
        $dbInstaller->dataExists('neic_catalog') &&
        $dbInstaller->dataExists('neic_response')
      )
    )
  );

if (!$answer) {
  echo "Skipping NEIC.\n";
  return;
}


$neicSql = configure('NEIC_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'neic.sql',
    "NEIC regions schema script");
$dbInstaller->runScript($neicSql);
echo "Success!!\n";

// download NEIC data
echo "\nDownloading and loading NEIC data:\n";
$filenames = array('neiccatalog.zip', 'neicresponse.zip');
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'neic'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  $url = $geoserveData->getUrl($filename);
  downloadURL($url, $downloaded_file);

  // uncompress NEIC data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// NEIC data load
// ----------------------------------------------------------------------

// NEIC Catalog

echo "\nLoading NEIC catalog data ... ";

// load json into temporary table
$dbInstaller->run('
  CREATE TABLE neic_catalog_json (
    id INTEGER PRIMARY KEY,
    name VARCHAR(50),
    magnitude DECIMAL(2, 1),
    type VARCHAR(50),
    shape JSON
  )
');
$dbInstaller->copyFrom($download_path . 'neiccatalog.csv', 'neic_catalog_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO neic_catalog (
    SELECT
      id,
      name,
      magnitude,
      type,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM neic_catalog_json
  )
');
echo "SUCCESS!!\n";

// NEIC Response

echo 'Loading NEIC response data ... ';

// load json into temporary table
$dbInstaller->run('
  CREATE TABLE neic_response_json (
    id INTEGER PRIMARY KEY,
    name VARCHAR(50),
    magnitude DECIMAL(2, 1),
    type VARCHAR(50),
    shape JSON
  )
');
$dbInstaller->copyFrom($download_path . 'neicresponse.csv', 'neic_response_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO neic_response (
    SELECT
      id,
      name,
      magnitude,
      type,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM neic_response_json
  )
');
  echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// NEIC data clean-up
// ----------------------------------------------------------------------

print 'Cleaning up downloaded data ... ';
$downloads = scandir($download_path);
foreach ($downloads as $download) {
  if (!is_dir($download)) {
    unlink($download_path . $download);
  }
}
rmdir($download_path);
print "SUCCESS!!\n";

?>
