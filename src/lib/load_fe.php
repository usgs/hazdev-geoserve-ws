<?php

// ----------------------------------------------------------------------
// FE/FE Renames data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating FE (and renames) dataset. Existing data will be deleted, " .
    'continue?', (DB_FULL_LOAD ||
      !(
        $dbInstaller->dataExists('fe') &&
        $dbInstaller->dataExists('fe_rename')
      )
    )
  );

if (!$answer) {
  echo "Skipping FE.\n";
  return;
}


$feSql = configure('FE_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'fe.sql',
    "FE regions schema script");
$dbInstaller->runScript($feSql);
echo "Success!!\n";

// download FE data
echo "\nDownloading and loading FE data:\n";
$filenames = array(
  'fe.zip',
  'ferename.zip'
);
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'FE'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  $url = $geoserveData->getUrl($filename);
  downloadURL($url, $downloaded_file);

  // uncompress FE data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// FE data load
// ----------------------------------------------------------------------

// FE

echo "\nLoading FE data ... ";

// load json into temporary table
$dbInstaller->run('
  CREATE TEMPORARY TABLE fe_json (
    id     INTEGER PRIMARY KEY,
    num    INTEGER,
    place  VARCHAR(100),
    shape  JSON
  )
');
$dbInstaller->copyFrom($download_path . 'fe.csv', 'fe_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO fe (
    SELECT
      id,
      num,
      place,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM fe_json
  )
');
echo "SUCCESS!!\n";

// FE Renames

echo 'Loading FE Renames data ... ';
$dbInstaller->run('
  CREATE TEMPORARY TABLE fe_rename_json (
    id     INTEGER PRIMARY KEY,
    place  VARCHAR(100),
    shape  JSON
  )
');
$dbInstaller->copyFrom($download_path . 'ferename.csv', 'fe_rename_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO fe_rename (
    SELECT
      id,
      place,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM fe_rename_json
  )
');
echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// FE data clean-up
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
