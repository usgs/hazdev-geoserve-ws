<?php

// ----------------------------------------------------------------------
// FE/FE Renames data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nWould you like to download and load FE (and renames) data",
    true
  );

if (!$answer) {
  echo "Skpping FE.\n";
  return;
}

$answer = promptYesNo("The schema must already exist in order to " .
    "load FE (and renames) data, continue", true);

if (!$answer) {
  echo "Skipping FE.\n";
  return;
}

// download FE data
$url = configure('FE_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/FE/',
    "FE download url");
$filenames = array('fe.dat', 'ferenames.dat');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'FE'
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
// Remove FE data from tables
// ----------------------------------------------------------------------

// Delete from fe
$dbInstaller->run('DELETE FROM fe');

// Delete from fe_rename
$dbInstaller->run('DELETE FROM fe_rename');


// ----------------------------------------------------------------------
// FE data load into temp tables
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
