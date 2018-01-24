<?php

include_once 'install/DatabaseInstaller.class.php';
include_once 'install-funcs.inc.php';

// Read in DSN
include_once '../conf/config.inc.php';

$defaultScriptDir = getcwd() . '/sql/pgsql/';

// Remove the database
$answer = configure('DO_SCHEMA_LOAD', 'Y',
    "\nWould you like to remove the data for this application");

if (!responseIsAffirmative($answer)) {
  print "Normal exit.\n";
  exit(0);
}

$answer = configure('CONFIRM_DO_SCHEMA_LOAD', 'N',
    "\nRemoving the data removes any existing database, schema, users, and/or data.\n" .
    'Are you sure you wish to continue');

if (!responseIsAffirmative($answer)) {
  print "\nNormal exit.\n";
  exit(0);
}

// Setup root DSN
$username = configure('DB_ADMIN_USER', 'root', "\nDatabase adminitrator user");
$password = configure('DB_ADMIN_PASSWORD', '', "Database administrator password",
    true);
$installer = DatabaseInstaller::getInstaller($CONFIG['DB_DSN'], $username, $password);

// Drop tables
$installer->runScript($defaultScriptDir . 'drop_tables.sql');

// Disable postgis
$installer->disablePostgis();

// Drop user
$installer->dropUser(array('SELECT'), $CONFIG['DB_USER']);

// Drop database
$installer->dropDatabase();

?>
