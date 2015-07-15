<?php

// ----------------------------------------------------------------------
// Authoritative data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nWould you like to download and load authoritative data",
    true
  );

if (!$answer) {
  echo "Skipping authoritative.\n";
  return;
}

$answer = promptYesNo("The tables must already exist in order to " .
    "load authoritative data, create eables", true);

if ($answer) {
  $authoritativeSql = configure('AUTHORITATIVE_SQL',
      $defaultScriptDir . DIRECTORY_SEPARATOR . 'authoritative.sql',
      "Authoritative regions schema script");
  $dbInstaller->runScript($authoritativeSql);
  echo "Success!!\n";
}

// download authoritative data
echo "\nDownloading and loading authoritative region data:\n";
$url = configure('AUTHORITATIVE_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/auth/',
    "Authoritative download url");
$filenames = array('authregions.dat');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'auth'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

  // uncompress authoritative data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}

// ----------------------------------------------------------------------
// Remove authoritative data from tables
// ----------------------------------------------------------------------

// Delete from authoritative
$dbInstaller->run('DELETE FROM authoritative');


// ----------------------------------------------------------------------
// Authoritative data load into temp tables
// ----------------------------------------------------------------------

// Authoritative

echo "\nLoading authoritative data ... ";
$dbInstaller->copyFrom($download_path . 'authregions.dat', 'authoritative',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
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
