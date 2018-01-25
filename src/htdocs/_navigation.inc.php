<?php

include_once '../lib/data/metadata.inc.php';

$index = $CONFIG['MOUNT_PATH'] . '/';
$uri = $_SERVER['REQUEST_URI'];

echo navGroup(
	navItem($index, 'Geoserve Web Services', $uri === $index || $uri === $index . 'index.php'),
	implode("", $endpointLinks));

echo navItem('/geoserve/', 'Interactive Interface');

?>
