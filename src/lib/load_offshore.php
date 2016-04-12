<?php

// ----------------------------------------------------------------------
// Offshore polygon data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating offshore region dataset. Existing data will be deleted, continue?",
    true
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
$url = configure('OFFSHORE_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/offshore/',
    "Offshore download url");
$filenames = array('feoffshore.dat');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fe'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

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
$dbInstaller->copyFrom($download_path . 'feoffshore.dat', 'offshore',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
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
