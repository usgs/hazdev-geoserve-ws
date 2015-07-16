<?php

// ----------------------------------------------------------------------
// NEIC data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating NEIC dataset. Existing data will be deleted, continue?",
    true
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
$url = configure('NEIC_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/neic/',
    "NEIC download url");
$filenames = array('neiccatalog.dat', 'neicresponse.dat');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'neic'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

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
$dbInstaller->copyFrom($download_path . 'neiccatalog.dat', 'neic_catalog',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
echo "SUCCESS!!\n";

// NEIC Response

echo 'Loading NEIC response data ... ';
$dbInstaller->copyFrom($download_path . 'neicresponse.dat', 'neic_response',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
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
