<?php

include_once '../conf/config.inc.php';

print navGroup('Geoserve Web Service',
  navItem('/ws/geoserve/', 'API Documention') .
  navItem('/search.php', 'URL Builder')
);

?>
