<?php

include_once '../lib/data/metadata.inc.php';


echo navItem($MOUNT_PATH . '/index.php', 'Geoserve');
echo navGroup(
    navItem($MOUNT_PATH . '/services.php', 'Geoserve Documentation'),
    implode("", $endpointLinks));

?>
