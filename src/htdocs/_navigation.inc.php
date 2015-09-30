<?php

include_once '../lib/data/metadata.inc.php';


echo navItem($MOUNT_PATH . '/index.php', 'Geoserve');
echo navGroup(
    navItem($MOUNT_PATH . '/services.html', 'Geoserve Documentation'),
    implode("", $endpointLinks));

?>
