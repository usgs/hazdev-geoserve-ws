<?php

include_once '../conf/config.inc.php';

print navGroup('Geoserve Web Service',
  navItem($MOUNT_PATH . '/index.php', 'API Documention') .
  navItem($MOUNT_PATH . '/search.php', 'URL Builder')
);

?>
