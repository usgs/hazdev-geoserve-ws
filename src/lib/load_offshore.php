<?php

// ----------------------------------------------------------------------
// Offshore polygon data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating offshore region dataset. Existing data will be deleted, continue?",
    (DB_FULL_LOAD || !$dbInstaller->dataExists('offshore'))
  );

if (!$answer) {
  echo "Skipping offshore regions.\n";
  return;
}


$offshoreSql = configure('ADMIN_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'offshore.sql',
    "offshore regions schema script");
$dbInstaller->runScript($offshoreSql);
echo "Success!!\n";

// download admin region data
echo "\nDownloading and loading offshore region data:\n";
$filenames = array('offshore.zip');
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'fe'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  $url = $geoserveData->getUrl($filename);
  downloadURL($url, $downloaded_file);

  // uncompress offshore data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// Offshore data load
// ----------------------------------------------------------------------

// Offshore

echo "\nLoading offshore data ... ";
// load json into temporary table
$dbInstaller->run('
  CREATE TABLE offshore_json (
    id        INTEGER PRIMARY KEY,
    name      VARCHAR(255),
    shape     JSON
  )
');
$dbInstaller->copyFrom($download_path . 'offshore.csv', 'offshore_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO offshore (
    SELECT
      id,
      name,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM offshore_json
  )
');

echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Offshore data clean-up
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
