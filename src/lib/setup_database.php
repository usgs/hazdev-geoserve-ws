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

$CONFIG_FILE = '../conf/config.ini';
$DO_DATA_LOAD = (basename($argv[0]) === 'setup_database.php');

include_once 'install-funcs.inc.php';
include_once 'install/DatabaseInstaller.class.php';

// Initial configuration stuff
if (!file_exists($CONFIG_FILE)) {
  print "$CONFIG_FILE not found. Please configure the application " .
      'before trying to set up the database. Configuration can be ' .
      "done as part of the installation process.\n";
  exit(-1);
}
$CONFIG = parse_ini_file($CONFIG_FILE);
$DB_DSN = configure('DB_ROOT_DSN', '', 'Database administrator DSN');
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

$answer = configure('DO_SCHEMA_LOAD', 'Y',
    "\nWould you like to create the database schema");

if (!responseIsAffirmative($answer)) {
  print "Normal exit.\n";
  exit(0);
}

$answer = configure('CONFIRM_DO_SCHEMA_LOAD', 'N',
    "Loading the schema removes any existing schema and/or data.\n" .
    'Are you sure you wish to continue');

if (!responseIsAffirmative($answer)) {
  print "Normal exit.\n";
  exit(0);
}

// Find schema load file
$schemaScript = configure('SCHEMA_SCRIPT',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'create_tables.sql',
    'SQL script containing "create" schema definition');
if (!file_exists($schemaScript)) {
  print "The indicated script does not exist. Please try again.\n";
  exit(-1);
}
$dropSchemaScript = configure('SCHEMA_SCRIPT',
    str_replace('create_tables.sql', 'drop_tables.sql', $schemaScript),
    'SQL script containing "drop" schema definition');
if (!file_exists($dropSchemaScript)) {
  print "The indicated script does not exist. Please try again.\n";
  exit(-1);
}


include_once 'install/DatabaseInstaller.class.php';
$dbInstaller = DatabaseInstaller::getInstaller($DB_DSN, $username, $password);

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

// run drop tables
$dbInstaller->runScript($dropSchemaScript);
// create schema
$dbInstaller->runScript($schemaScript);
// create read user
$dbInstaller->createUser(array('SELECT'), $CONFIG['DB_USER'], $CONFIG['DB_PASS']);

print "Schema loaded successfully!\n";


// ----------------------------------------------------------------------
// Cities data download/unizp
// ----------------------------------------------------------------------


// TODO:: prompt user to download geoname data (cities1000.zip, US.zip)
// TODO:: download geoname data
// TODO:: unzip geoname data


// ----------------------------------------------------------------------
// Geoserve data loading
// ----------------------------------------------------------------------


print "Loading geoname polygon data ... ";
include_once 'load_cities.php';
print "SUCCESS!!\n";

print "Loading authoritative region polygon data ... ";
$dbInstaller->runScript($defaultDataDir . '/authoritative_region.sql');
print "SUCCESS!!\n";

print "Loading authoritative region us polygon data ... ";
$dbInstaller->runScript($defaultDataDir . '/authoritative_region_us.sql');
print "SUCCESS!!\n";

print "Loading country polygon data ... ";
$dbInstaller->runScript($defaultDataDir . '/country.sql');
print "SUCCESS!!\n";

print "Loading state polygon data ... ";
$dbInstaller->runScript($defaultDataDir . '/state.sql');
print "SUCCESS!!\n";

// print "Loading fe region polygon data ...";
// $dbInstaller->runScript($defaultDataDir . '/fe.sql');
// print "SUCCESS!!\n";

// print "Loading fe rename region polygon data ...";
// $dbInstaller->runScript($defaultDataDir . '/fe_rename.sql');
// print "SUCCESS!!\n";

// print "Loading fe plus region polygon data ...";
// $dbInstaller->runScript($defaultDataDir . '/fe_plus.sql');
// print "SUCCESS!!\n";

// print "Loading tectonic summary region polygon data ...";
// $dbInstaller->runScript($defaultDataDir . '/tectonic_summary_region.sql');
// print "SUCCESS!!\n";


// ----------------------------------------------------------------------
// End of database setup
// ----------------------------------------------------------------------

print "\nNormal exit.\n";
exit(0);
