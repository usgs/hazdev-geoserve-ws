<?php

include_once '../conf/config.inc.php';

print navGroup('Geoserve Web Service',
  navItem($MOUNT_PATH . '/index.php', 'Places Documentation') .
  navItem($MOUNT_PATH . '/RegionSearch.php', 'Regions API Documentation')
);

?>
