<?php

// entry point into the FDSN Event Webservice

if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';
  $TITLE = 'Geoserve';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="index.css"/>';
  $FOOT =
      '<script>' .
        'var MOUNT_PATH=' . json_encode($MOUNT_PATH) . ';' .
      '</script>' .
      '<script src="index.js"></script>';
  include 'template.inc.php';
}
?>

<div id="location">
  <noscript>
    This application requires javascript.
  </noscript>
</div>
