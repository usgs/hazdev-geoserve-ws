<?php

date_default_timezone_set('UTC');

$OLD_PWD = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : '';

// work from lib directory
chdir(dirname($argv[0]));


if ($argv[0] === './pre-install.php' || (isset($_SERVER['PWD']) && $_SERVER['PWD'] !== $OLD_PWD)) {
  // pwd doesn't resolve symlinks
  $LIB_DIR = $_SERVER['PWD'];
} else {
  // windows doesn't update $_SERVER['PWD']...
  $LIB_DIR = getcwd();
}
$APP_DIR = dirname($LIB_DIR);
$HTDOCS_DIR = $APP_DIR . '/htdocs';
$CONF_DIR = $APP_DIR . '/conf';

$HTTPD_CONF = $CONF_DIR . '/httpd.conf';
$CONFIG_FILE_INI = $CONF_DIR . '/config.ini';
$CONFIG_FILE_PHP = $CONF_DIR . '/config.inc.php';

chdir($LIB_DIR);

if (!is_dir($CONF_DIR)) {
  mkdir($CONF_DIR, 0755, true);
}


// configuration defaults
$DEFAULTS = array(
  'MOUNT_PATH' => '/ws/geoserve',
  'GEOSERVE_VERSION' => '0.1.0',
  'DB_DSN' => 'pgsql:host=localhost;port=5432;dbname=earthquake;',
  'DB_SCHEMA' => 'geoserve',
  'DB_USER' => 'web',
  'DB_PASS' => ''
);
$HELP_TEXT = array(
  'MOUNT_PATH' => 'Url path to application',
  'GEOSERVE_VERSION' => 'Webservice api version',
  'DB_DSN' => 'Database connection DSN string',
  'DB_SCHEMA' => 'Schema name for database installation',
  'DB_USER' => 'Read/write username for database connections',
  'DB_PASS' => 'Password for database user'
);

foreach ($argv as $arg) {
  if ($arg === '--non-interactive') {
    define('NON_INTERACTIVE', true);
  }

  if ($arg === '--full') {
    define('DB_FULL_LOAD', true);
  }

  if ($arg === '--skip-db') {
    define('SKIP_DB', true);
  }
}

if (!defined('NON_INTERACTIVE')) {
  define('NON_INTERACTIVE', false);
}

if (!defined('DB_FULL_LOAD')) {
  define('DB_FULL_LOAD', false);
}

if (!defined('SKIP_DB')) {
  define('SKIP_DB', false);
}



// Interactively prompts user for config. Writes CONFIG_FILE_INI
include_once 'configure.inc.php';


// Parse the configuration
include_once '../conf/config.inc.php';


// Write the HTTPD configuration file
file_put_contents($HTTPD_CONF, '
  ## autogenerated at ' . date('r') . '

  Alias ' . $CONFIG['MOUNT_PATH'] . ' ' . $HTDOCS_DIR . '

  RewriteEngine on
  RewriteRule ^' . $CONFIG['MOUNT_PATH'] . '/(services|places|regions|layers)\.(json)$ ' . $CONFIG['MOUNT_PATH'] . '/$1.php?format=$2 [L,PT,QSA]

  <Location ' . $CONFIG['MOUNT_PATH'] . '>
    ExpiresActive on
    ExpiresDefault "access plus 1 days"

    Header set Access-Control-Allow-Origin "*" env=!NO_CORS
    Header set Access-Control-Allow-Methods "*" env=!NO_CORS
    Header set Access-Control-Allow-Headers "accept,origin,authorization,content-type" env=!NO_CORS

    # apache 2.2
    <IfModule !mod_authz_core.c>
      Order allow,deny
      Allow from all

      <LimitExcept GET>
        Deny from all
      </LimitExcept>
    </IfModule>

    # apache 2.4
    <IfModule mod_authz_core.c>
      Require all granted

      <LimitExcept GET>
        Require all denied
      </LimitExcept>
    </IfModule>
  </Location>
');


// configure database
echo "\n";
if (promptYesNo('Would you like to setup the database or load data',
    !SKIP_DB && NON_INTERACTIVE)) {
  include_once 'setup_database.php';
}
