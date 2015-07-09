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
chdir(dirname($argv[0]));
include_once 'install-funcs.inc.php';
include_once 'install/DatabaseInstaller.class.php';
include_once '../conf/config.inc.php';


$DB_DSN = configure('DB_ROOT_DSN', $CONFIG['DB_DSN'], 'Database administrator DSN');
$dbtype = substr($DB_DSN, 0, strpos($DB_DSN, ':'));
$username = configure('DB_ROOT_USER', 'root', 'Database adminitrator user');
$password = configure('DB_ROOT_PASS', '', 'Database administrator password',
    true);

$defaultScriptDir = implode(DIRECTORY_SEPARATOR, array(
    $APP_DIR, 'lib', 'sql', $dbtype));
$defaultDataDir = implode(DIRECTORY_SEPARATOR, array(
    $APP_DIR, 'lib', 'data'));


// ----------------------------------------------------------------------
// Schema loading configuration
// ----------------------------------------------------------------------


$dbInstaller = DatabaseInstaller::getInstaller($DB_DSN, $username, $password);

$answer = promptYesNo("Would you like to create the database schema", true);

if ($answer) {

  $answer = promptYesNo("\nLoading the schema removes any existing schema " .
      "and/or data.\nAre you sure you wish to continue", false);

  if ($answer) {

    // ----------------------------------------------------------------------
    // Prompt for create/drop sql scripts
    // ----------------------------------------------------------------------

    $schemaScript = configure('SCHEMA_SCRIPT',
        $defaultScriptDir . DIRECTORY_SEPARATOR . 'create_tables.sql',
        "SQL script containing \"create\" schema definition");
    if (!file_exists($schemaScript)) {
      print "The indicated script does not exist. Please try again.\n";
      exit(-1);
    }

    $dropSchemaScript = configure('SCHEMA_SCRIPT',
        str_replace('create_tables.sql', 'drop_tables.sql', $schemaScript),
        "SQL script containing \"drop\" schema definition");
    if (!file_exists($dropSchemaScript)) {
      print "The indicated script does not exist. Please try again.\n";
      exit(-1);
    }

    // ----------------------------------------------------------------------
    // Drop Database
    // ----------------------------------------------------------------------

    $dbInstaller->dropDatabase();

    // ----------------------------------------------------------------------
    // Create Database
    // ----------------------------------------------------------------------

    // make sure database exists
    if (!$dbInstaller->databaseExists()) {
      $dbInstaller->createDatabase();
    }


    // ----------------------------------------------------------------------
    // Create Schema
    // ----------------------------------------------------------------------

    echo 'Loading schema ... ';
    // run drop tables
    $dbInstaller->runScript($dropSchemaScript);
    // create schema
    $dbInstaller->runScript($schemaScript);
    // create read user
    $dbInstaller->createUser(array('SELECT'), $CONFIG['DB_USER'], $CONFIG['DB_PASS']);

    echo "SUCCESS!!\n";

  }

}


// ----------------------------------------------------------------------
// Data downloads and database loads
// ----------------------------------------------------------------------

include_once 'load_geonames.php';
include_once 'load_fe.php';

// ----------------------------------------------------------------------
// End of database setup
// ----------------------------------------------------------------------

echo "\nNormal exit.\n";
exit(0);
