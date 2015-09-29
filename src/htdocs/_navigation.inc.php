<?php

include_once '../lib/data/metadata.inc.php';

$navList = implode("", $endpointLinks);
print navGroup(navItem('/index.php', 'Geoserve Web Service'),
  $navList
);

?>
