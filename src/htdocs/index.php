<?php

if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';
  include_once '../lib/data/metadata.inc.php';
  include_once 'functions.inc.php';

  $format = param('format', 'html');

  if ($format === 'json') {
    // JSON output
    header('Content-Type: application/json');
    print str_replace("\\'", '"', json_encode($GEOSERVE_ENDPOINTS));
    exit(0);
  }

  // HTML output
  $TITLE = 'Geoserve Documentation';
  $NAVIGATION = True;

  include_once 'template.inc.php';
}
?>

<p>
  The Geoserve Web Service consists of
  <?php print count($GEOSERVE_ENDPOINTS); ?>
  endpoint<?php print (count($GEOSERVE_ENDPOINTS)>1)?'s':''; ?>. Each end
  point has a slightly different API and different available data. For more
  details about each endpoint, please click a link below.
</p>

<ul class="endpoints">
  <li><?php print implode('</li><li>', $endpointLinks); ?></li>
</ul>
