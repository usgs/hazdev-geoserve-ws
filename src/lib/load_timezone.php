<?php

// ----------------------------------------------------------------------
// Timezones data download
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating timezone dataset. Existing data will be deleted, continue?",
    true
  );

if (!$answer) {
  echo "Skipping timezone.\n";
  return;
}


$timezoneSql = configure('TIMEZONE_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'timezone.sql',
    "Timezone regions schema script");
$dbInstaller->runScript($timezoneSql);
echo "Success!!\n";

// download timezone region data
echo "\nDownloading and loading timezone region data:\n";
$url = configure('GLOBAL_ADMIN_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/timezones/',
    "Timezone download url");
$filenames = array('timezones.dat');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'timezone'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);
}


// ----------------------------------------------------------------------
// timezone data load
// ----------------------------------------------------------------------

// TIMEZONE

echo "\nLoading timezone data ... ";
$dbInstaller->copyFrom($download_path . 'timezones.dat', 'timezone',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Timezone data clean-up
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
