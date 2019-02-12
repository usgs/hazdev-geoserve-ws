<?php

// ----------------------------------------------------------------------
// Authoritative data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating authoritative dataset. Existing data will be deleted, continue?",
    (DB_FULL_LOAD || !$dbInstaller->dataExists('authoritative'))
  );

if (!$answer) {
  echo "Skipping authoritative.\n";
  return;
}


$authoritativeSql = configure('AUTHORITATIVE_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'authoritative.sql',
    "Authoritative regions schema script");
$dbInstaller->runScript($authoritativeSql);
echo "Success!!\n";

// download authoritative data
echo "\nDownloading and loading authoritative region data:\n";
$filenames = array('authregions.zip');
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'auth'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  $url = $geoserveData->getUrl($filename);
  downloadURL($url, $downloaded_file);

  // uncompress authoritative data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// Authoritative data load
// ----------------------------------------------------------------------

// Authoritative

echo "\nLoading authoritative data ... ";
// load json into temporary table
$dbInstaller->run('
  CREATE TABLE authoritative_json (
    id        INTEGER PRIMARY KEY,
    name      VARCHAR(10),
    type      VARCHAR(50),
    priority  INTEGER,
    network   VARCHAR(10),
    shape     JSON
  )
');
$dbInstaller->copyFrom($download_path . 'authregions.dat', 'authoritative_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO authoritative (
    SELECT
      id,
      name,
      type,
      priority,
      network,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM authoritative_json
  )
');
echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Authoritative data clean-up
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
