<?php

// ----------------------------------------------------------------------
// Admin data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating administrative dataset. Existing data will be deleted, continue?",
    (DB_FULL_LOAD || !$dbInstaller->dataExists('admin'))
  );

if (!$answer) {
  echo "Skipping administrative.\n";
  return;
}


$adminSql = configure('ADMIN_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'admin.sql',
    "Admin regions schema script");
$dbInstaller->runScript($adminSql);
echo "Success!!\n";

// download admin region data
echo "\nDownloading and loading admin region data:\n";
$filenames = array('globaladmin.zip');
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'admin'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  $url = $geoserveData->getUrl($filename);
  downloadURL($url, $downloaded_file);

  // uncompress admin data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// Admin data load
// ----------------------------------------------------------------------

// Admin

echo "\nLoading admin data ... ";

// load json into temporary table
$dbInstaller->run('
  CREATE TABLE admin_json (
    id       INTEGER PRIMARY KEY,
    iso      CHAR(3),
    country  VARCHAR(100),
    region   VARCHAR(100),
    shape    JSON
  )
');
$dbInstaller->copyFrom($download_path . 'globaladmin.dat', 'admin_json',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
// convert json to postgis geometry
$dbInstaller->run('
  INSERT INTO admin (
    SELECT
      id,
      iso,
      country,
      region,
      ST_SetSRID(ST_GeomFromGeoJSON(shape), 4326)
    FROM admin_json
  )
');
echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Admin data clean-up
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
