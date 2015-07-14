<?php

// ----------------------------------------------------------------------
// Global admin data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nWould you like to download and load global admin data",
    true
  );

if (!$answer) {
  echo "Skpping global admin.\n";
  return;
}

$answer = promptYesNo("The schema must already exist in order to " .
    "load global admin data, create schema", true);

if ($answer) {
  $adminSql = configure('ADMIN_SQL',
      $defaultScriptDir . DIRECTORY_SEPARATOR . 'admin.sql',
      "Admin regions schema script");
  $dbInstaller->runScript($adminSql);
  echo "Success!!\n";
}

// download global admin data
echo "\nDownloading and loading admin region data:\n";
$url = configure('GLOBAL_ADMIN_URL',
    'ftp://hazards.cr.usgs.gov/web/hazdev-geoserve-ws/admin/',
    "Global admin download url");
$filenames = array('globaladmin.zip');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'admin'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

  // uncompress global admin data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}

// ----------------------------------------------------------------------
// Remove global admin data from tables
// ----------------------------------------------------------------------

// Delete from global admin
$dbInstaller->run('DELETE FROM globaladmin');


// ----------------------------------------------------------------------
// Global admin data load
// ----------------------------------------------------------------------

// Global admin

echo "\nLoading global admin data ... ";
$dbInstaller->copyFrom($download_path . 'globaladmin.dat', 'globaladmin',
    array('NULL AS \'\'', 'CSV', 'HEADER'));
echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Global admin data clean-up
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
