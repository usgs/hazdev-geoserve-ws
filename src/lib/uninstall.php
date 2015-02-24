<?php

include_once 'install/DatabaseInstaller.class.php';
include_once 'install-funcs.inc.php';

// Read in DSN
$CONFIG_FILE = '../conf/config.ini';
$CONFIG = parse_ini_file($CONFIG_FILE);

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
$username = configure('DB_ROOT_USER', 'root', "\nDatabase adminitrator user");
$password = configure('DB_ROOT_PASS', '', "Database administrator password",
    true);
$installer = DatabaseInstaller::getInstaller($CONFIG['DB_DSN'], $username, $password);

// Drop user
$installer->dropUser(array('SELECT'), $CONFIG['DB_USER']);

// Drop database
$installer->dropDatabase();

?>
