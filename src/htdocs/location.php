<?php

// entry point into the FDSN Event Webservice

if (!isset($TEMPLATE)) {
  $TITLE = 'Location';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="location.css"/>';
  $FOOT = '<script src="location.js"></script>';
  include 'template.inc.php';
}
?>

<div id="location">
  <noscript>
    This application requires javascript.
  </noscript>
</div>
