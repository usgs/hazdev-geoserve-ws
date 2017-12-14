<?php
if (!isset($TEMPLATE)) {
  include_once '../lib/data/metadata.inc.php';
  include_once 'functions.inc.php';

  if ($format === 'json') {
    // JSON output
    header('Content-Type: application/json');

    $CACHE_MAXAGE = 900;
    include_once '../lib/cache.inc.php';

    if (count($_GET) <= 1) {
      // Usage
      print str_replace("\\'", '"', json_encode($GEOSERVE_LAYERS));
    } else {
      try {
        include_once '../conf/service.inc.php';
        $SERVICE->layers($_GET);
      } catch (Exception $e) {
        trigger_error($e->getMessage());
        $SERVICE->error('500', 'Server Error');
      }
    }
    exit(0);
  }

  // HTML output
  $TITLE = 'Geoserve Layers Endpoint';
  $HEAD = '<link rel="stylesheet" href="endpoint.css"/>';
  $NAVIGATION = true;

  if (!function_exists('pinfo')) {
    function enumeration ($values) {
      $types = '';

      foreach ($values as $value) {
        $types .= '<dt><code>' . $value['name'] . '</code></dt>' .
            '<dd>' . $value['description'] . '</dd>';
      }

      return '<dl class="vertical types">' . $types . '</dl>';
    }

    function pinfo ($p) {
      $result = $p['description'];

      if (isset($p['minimum']) && isset($p['maximum'])) {
        $result .= ' [' . $p['minimum'] . ', ' . $p['maximum'] . '].';
      } else if (isset($p['minimum'])) {
        $result .= ' [' . $p['minimum'] . ', +Inf].';
      } else if (isset($p['maximum'])) {
        $result .= ' [-Inf, ' . $p['maximum'] . '].';
      }

      if (isset($p['values'])) {
        $result .= enumeration($p['values']);
      }

      return $result;
    }

    function formatTitle ($title) {
      $formatted = ucfirst($title);

      if ($title === 'fe') {
        $formatted = 'FE';
      } else if ($title === 'neicresponse') {
        $formatted = 'NEIC Response';
      } else if ($title === 'neiccatalog') {
        $formatted = 'NEIC Catalog';
      } else if ($title === 'tectonic') {
        $formatted = 'Tectonic Summary';
      }

      return $formatted;
    }
  }

  include_once 'template.inc.php';
}
?>

<?php print $GEOSERVE_LAYERS['description']; ?>

<h2 id="request">Request</h2>

<h3>Syntax</h3>
<p>
  A geoserve <em>places</em> search takes the following form:
</p>
<pre>
  <code><?php print $GEOSERVE_LAYERS['url']; ?></code>
</pre>

<h3>Parameters</h3>
<?php foreach ($GEOSERVE_LAYERS['parameters'] as $title=>$info) : ?>
  <h4 id="parameter-<?php print $title; ?>">
    <?php print ucfirst($title); ?> Search Parameters
  </h4>
  <ul class="parameters vertical separator no-style">
    <?php foreach ($info as $name=>$parameter) : ?>
      <li id="<?php print $name; ?>">
        <header>
          <code><?php print $name; ?></code>
          <small><?php print $parameter['type']; ?></small>
        </header>
        <section>
          <?php
            print pinfo($parameter);
          ?>
        </section>
      </li>
    <?php endforeach ?>
  </ul>
<?php endforeach ?>

<h3>Notes</h3>
<?php foreach ($GEOSERVE_LAYERS['notes'] as $note) : ?>
  <aside class="parameter-note">
    <?php print $note; ?>
  </aside>
<?php endforeach ?>


<h2 id="response">Response</h2>
<p>
  The response is formatted as a
  <a href="http://geojson.org/geojson-spec.html#feature-collection-objects">
  GeoJSON FeatureCollections</a> nested in a top-level response object. The
  nested GeoJSON FeatureCollections is keyed by the request <code>type</code>.
</p>

<h3>Properties</h3>
<p>
  Each returned Feature in the GeoJSON FeatureCollection includes an id,
  a geometry object with longitude, latitude, and elevation,
  and a properties object with the following attributes:
</p>

<?php foreach ($GEOSERVE_LAYERS['output'] as $title=>$output) : ?>
  <h4><?php print formatTitle($title); ?></h4>
  <ul class="parameters vertical separator no-style">
    <?php foreach ($output as $name=>$info) : ?>
      <li id="<?php print $title . '-' . $name; ?>">
        <header>
          <code><?php print $name; ?></code>
          <small><?php print $info['type']; ?></small>
        </header>
        <section>
          <?php print $info['description']; ?>
        </section>
      </li>
    <?php endforeach ?>
  </ul>
<?php endforeach ?>

<h2 id="example">Examples</h2>
<p>
  Below are example requests and responses that detail the nested GeoJSON
  structure. Each type has a nested GeoJSON FeatureCollection that may contain
  multiple GeoJSON features. Click the link for an example to see the response.
</p>

<ul>
  <?php foreach ($GEOSERVE_LAYERS['examples'] as $example) : ?>
    <li>
      <p><?php print $example['description']; ?>:</p>
      <a href="<?php print $example['url']; ?>">
        <?php print $example['url']; ?>
      </a>
    </li>
  <?php endforeach ?>
</ul>
