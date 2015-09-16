<?php

// ----------------------------------------------------------------------
// Tectonic Summary data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating Tectonic Summary dataset. Existing data will be deleted, " .
    " continue?",
    true
  );

if (!$answer) {
  echo "Skipping tectonic summary.\n";
  return;
}


$tectonicSql = configure('TECTONIC_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'tectonicsummary.sql',
    "Tectonic summary schema script");
$dbInstaller->runScript($tectonicSql);
echo "Success!!\n";


// download tectonic summary data
echo "\nDownloading and loading tectonic summary data:\n";
$url = configure('GLOBAL_TECTONIC_SUMMARY_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/tectonic/',
    "Tectonic summary download url");
$filenames = array('tectonicsummary.zip');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tectonic_summary'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
echo $download_path;
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

  // uncompress tectonic summary data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// Tectonic Summary data load
// ----------------------------------------------------------------------

// Tectonic Summary

echo "\nLoading tectonic summary data ... ";
$dbInstaller->copyFrom($download_path . 'tectonicsummary_nc.dat',
    'tectonic_summary', array('NULL AS \'\'', 'CSV', 'HEADER'));
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
