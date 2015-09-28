<?php
echo navGroup(navItem('/ws/geoserve/index.php', 'Geoserve Web Service'),
  navItem('/ws/geoserve/places.php', 'Places Details') .
  navItem('/ws/geoserve/regions.php', 'Regions Details') .
  navItem('/ws/geoserve/layers.php', 'Layers Details') .
  navItem('/ws/geoserve/location.php', 'Location Details')
);
?>
