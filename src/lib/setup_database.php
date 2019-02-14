<?php
// This script prompts user if they would like to set up the database this
// includes the following steps.
//
// (1) Create the database schema
//     (1.1) If initially answering yes, notify current content may be
//           destroyed. If user confirms descision, wipe existing database and
//           create a new schema using script.
// (2) Load reference data into the database.
// (3) [Only if script is run directly]
//     Load observation data into database.
//
// Note: If the user declines any step along the way this script is complete.

date_default_timezone_set('UTC');

// work from lib directory
chdir($LIB_DIR);
include_once 'install-funcs.inc.php';
include_once 'install/DatabaseInstaller.class.php';
include_once 'install/ScienceBaseItem.class.php';
include_once '../conf/config.inc.php';


// load data from geoserve data release
$geoserveData = new ScienceBaseItem('5a6f547de4b06e28e9caca43');


$DB_DSN = (isset($CONFIG['DB_ADMIN_DSN'])) ? $CONFIG['DB_ADMIN_DSN'] :
    configure('DB_ADMIN_DSN', $CONFIG['DB_DSN'], 'Database administrator DSN');
$dbtype = substr($DB_DSN, 0, strpos($DB_DSN, ':'));

$username = (isset($CONFIG['DB_ADMIN_USER'])) ? $CONFIG['DB_ADMIN_USER'] :
    configure('DB_ADMIN_USER', $CONFIG['DB_USER'], 'Database adminitrator user');
$password = (isset($CONFIG['DB_ADMIN_PASSWORD'])) ? $CONFIG['DB_ADMIN_PASSWORD'] :
    configure('DB_ADMIN_PASSWORD', $CONFIG['DB_PASS'],
    'Database administrator password', true);

$defaultScriptDir = implode(DIRECTORY_SEPARATOR, array(
    $APP_DIR, 'lib', 'sql', $dbtype));
$downloadBaseDir = isset($CONFIG['DOWNLOAD_DIR']) ?
    $CONFIG['DOWNLOAD_DIR'] : sys_get_temp_dir();


// ----------------------------------------------------------------------
// Schema loading configuration
// ----------------------------------------------------------------------

$dbInstaller = DatabaseInstaller::getInstaller($DB_DSN, $username, $password);

if (DB_FULL_LOAD) {
  $dbInstaller->dropDatabase();
}

if (!$dbInstaller->databaseExists()) {
  $answer = promptYesNo("Database does not exist, create it now?", true);

  if ($answer) {
    $dbInstaller->createDatabase();
  } else {
    echo "No database. Can not continue.\n";
  }
}

$dbInstaller->connect();


if (!$dbInstaller->postgisEnabled()) {
  $answer = promptYesNo("Postgis is not yet enabled. Enable it now?", true);

  if ($answer) {
    $dbInstaller->enablePostgis();
  } else {
    echo "Postgis not enabled. This may cause problems in a minute here.\n";
  }
}


// ----------------------------------------------------------------------
// Create User
// ----------------------------------------------------------------------

if (!$dbInstaller->userExists($CONFIG['DB_USER'])) {
  $answer = promptYesNo("Readonly user does not yet exist. Create it now?",
      true);

  if ($answer) {
    $dbInstaller->createUser(array('SELECT'), $CONFIG['DB_USER'],
        $CONFIG['DB_PASS']);
  }
}


// ----------------------------------------------------------------------
// Create Schema
// ----------------------------------------------------------------------

if (isset($CONFIG['DB_SCHEMA']) && $CONFIG['DB_SCHEMA'] !== '') {

  if (!$dbInstaller->schemaExists($CONFIG['DB_SCHEMA'])) {
    $answer = promptYesNo("Schema does not yet exist. Create it now?", true);

    if ($answer) {
      $dbInstaller->createSchema($CONFIG['DB_SCHEMA']);
    }
  }

  $dbInstaller->run('SET search_path = ' . $CONFIG['DB_SCHEMA'] . ', public');
}


// ----------------------------------------------------------------------
// Data downloads and database loads
// ----------------------------------------------------------------------

include_once 'load_geonames.php';
include_once 'load_fe.php';
include_once 'load_admin.php';
include_once 'load_authoritative.php';
include_once 'load_neic.php';
include_once 'load_tectonicsummary.php';
// Currently no timezone data. Skip.
// include_once 'load_timezone.php';
include_once 'load_offshore.php';

// run vacuum and analyze after loading data
$dbInstaller->run('VACUUM ANALYZE');

// ----------------------------------------------------------------------
// Grant Roles
// ----------------------------------------------------------------------

// read-only access
$dbInstaller->grantRoles(array('SELECT'), $CONFIG['DB_USER'],
    $CONFIG['DB_SCHEMA']);


// ----------------------------------------------------------------------
// End of database setup
// ----------------------------------------------------------------------

echo "\nNormal exit.\n";
exit(0);
