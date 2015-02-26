<?php

if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';

  $TITLE = 'Geoserve URL Builder';
  $NAVIGATION = true;
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
  <h3>Places</h3>
</header>

  <form method="get" action="places" class="vertical places">

    <fieldset>
      <legend>Required Parameters</legend>
      <label for="latitude">
        <code>latitude</code>
        <small>Latitude in decimal degrees. [-90,90] degrees.</small>
      </label>
      <input id="latitude" name="latitude" type="number"
          min="-90" max="90" step="0.001" required="required"/>

      <label for="longitude">
        <code>longitude</code>
        <small>Longitude in decimal degrees. [-180,180] degrees.</small>
      </label>
      <input id="longitude" name="longitude" type="number"
          min="-180" max="180" step="0.001" required="required"/>

      <label for="maxradiuskm">
        <code>maxradiuskm</code>
        <small>Search radius (in kilometers) from latitude, longitude.</small>
      </label>
      <input id="maxradiuskm" name="maxradiuskm" type="number" min="0" step="0.001"/>
    </fieldset>

    <fieldset>
      <legend>Optional Parameters</legend>
      <label for="limit">
        <code>limit</code>
        <small>Return at most this number of places, sorted by distance.</small>
      </label>
      <input id="limit" name="limit" type="number" min="0"/>

      <label for="minpopulation">
        <code>minpopulation</code>
        <small>Only return places with a minimum of this number of people.</small>
      </label>
      <input id="minpopulation" name="minpopulation" type="number" min="0"/>
    </fieldset>

    <button>Search Places</button>

  </form>

  <section class="search-url"></section>
  <section class="search-results"></section>

</section>