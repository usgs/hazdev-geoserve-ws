<?php

// ----------------------------------------------------------------------
// FE/FE Renames data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating FE (and renames) dataset. Existing data will be deleted, " .
    'continue?', true
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
$url = configure('FE_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/FE/',
    "FE download url");
$filenames = array('fe.dat', 'ferenames.dat');
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'FE'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

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
$dbInstaller->copyFrom($download_path . 'fe.dat', 'fe',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
echo "SUCCESS!!\n";

// FE Renames

echo 'Loading FE Renames data ... ';
$dbInstaller->copyFrom($download_path . 'ferenames.dat', 'fe_rename',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
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
