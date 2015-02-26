<?php

if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';

  $TITLE = 'Geoserve Search';
  $HEAD = '<link rel="stylesheet" href="search.css"/>';
  $FOOT = '
    <script>/*<![CDATA[*/
      var HOST_URL_PREFIX = \'' . $HOST_URL_PREFIX . '\';
      var MOUNT_PATH = \'' . $MOUNT_PATH . '\';
    /*]]>*/</script>
    <script src="search.js"></script>
  ';

  include_once 'template.inc.php';
}

?>

<section class="places-search">
  <header>
    <h2>Places</h2>
  </header>

  <form method="get" action="places" class="vertical places">

    <legend>Location</legend>
    <fieldset class="row">

      <div class="column one-of-two">
        <label for="latitude">Latitude [-90, 90]</label>
        <input id="latitude" name="latitude" type="number"
            min="-90" max="90" step="0.001" required="required"/>
      </div>

      <div class="column one-of-two">
        <label for="longitude">Longitude [-180, 180]</label>
        <input id="longitude" name="longitude" type="number"
            min="-180" max="180" step="0.001" required="required"/>
      </div>

    </fieldset>

    <legend>Options</legend>
    <fieldset class="form-options">

      <label for="maxradiuskm">Max Distance (km)</label>
      <input id="maxradiuskm" name="maxradiuskm" type="number" min="0" step="0.001"/>

      <label for="minpopulation">Min Population</label>
      <input id="minpopulation" name="minpopulation" type="number" min="0"/>

      <label for="limit">Limit</label>
      <input id="limit" name="limit" type="number" min="0"/>

    </fieldset>

    <button>Search Places</button>

  </form>

  <section class="search-url"></section>
  <section class="search-results"></section>

</section>