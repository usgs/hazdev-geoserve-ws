<?php

// ----------------------------------------------------------------------
// Geonames data download/uncompress
// ----------------------------------------------------------------------

$answer = promptYesNo(
    "\nUpdating geonames dataset. Existing data will be deleted, continue?",
    (DB_FULL_LOAD || !$dbInstaller->dataExists('geoname'))
  );

if (!$answer) {
  echo "Skipping geonames.\n";
  return;
}


$geonamesSql = configure('GEONAMES_SQL',
    $defaultScriptDir . DIRECTORY_SEPARATOR . 'geonames.sql',
    "Geonames schema script");
$dbInstaller->runScript($geonamesSql);
echo "Success!!\n";

// download geoname data
echo "\nDownloading and loading geonames data:\n";
$filenames = array(
  'admin1CodesASCII.zip',
  'cities1000.zip',
  'countryInfo.zip',
  'US.zip'
);
$download_path = $downloadBaseDir . DIRECTORY_SEPARATOR . 'geonames'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);
foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  $url = $geoserveData->getUrl($filename);
  downloadURL($url, $downloaded_file);

  // uncompress geonames data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print 'Extracting ' . $downloaded_file . "\n";
    extractZip($downloaded_file, $download_path);
  }
}


// ----------------------------------------------------------------------
// Geonames data load
// ----------------------------------------------------------------------

// Cities

echo "\n";
echo 'Loading Cities1000 data ... ';
$dbInstaller->run('
  CREATE TEMPORARY TABLE places_ww (
    geoname_id         INT PRIMARY KEY,
    name               VARCHAR(200),
    ascii_name         VARCHAR(200),
    alternate_names    TEXT,
    latitude           FLOAT,
    longitude          FLOAT,
    feature_class      CHAR(1),
    feature_code       VARCHAR(10),
    country_code       CHAR(2),
    cc2                VARCHAR(60),
    admin1_code        VARCHAR(20),
    admin2_code        VARCHAR(80),
    admin3_code        VARCHAR(20),
    admin4_code        VARCHAR(20),
    population         BIGINT,
    elevation          INT,
    dem                INT,
    timezone           VARCHAR(40),
    modification_date  DATE
  )
');

$dbInstaller->copyFrom($download_path . 'cities1000.txt', 'places_ww');
echo "SUCCESS!!\n";

echo 'Loading US cities data ... ';
$dbInstaller->run('
  CREATE TEMPORARY TABLE places_us (
    geoname_id         INT PRIMARY KEY,
    name               VARCHAR(200),
    ascii_name         VARCHAR(200),
    alternate_names    TEXT,
    latitude           FLOAT,
    longitude          FLOAT,
    feature_class      CHAR(1),
    feature_code       VARCHAR(10),
    country_code       CHAR(2),
    cc2                VARCHAR(60),
    admin1_code        VARCHAR(20),
    admin2_code        VARCHAR(80),
    admin3_code        VARCHAR(20),
    admin4_code        VARCHAR(20),
    population         BIGINT,
    elevation          INT,
    dem                INT,
    timezone           VARCHAR(40),
    modification_date  DATE
  )
');

$dbInstaller->copyFrom($download_path . 'US.txt', 'places_us');
echo "SUCCESS!!\n";



// ----------------------------------------------------------------------
// Genames data load from temp tables into schema
// ----------------------------------------------------------------------

// Load/Merge data into geoname table
echo 'Merging Cities1000 and US cities ... ';
$dbInstaller->run('
  INSERT INTO geoname (
    SELECT
      DISTINCT ON (geoname_id) geoname_id,
      name,
      ascii_name,
      alternate_names,
      latitude,
      longitude,
      feature_class,
      feature_code,
      country_code,
      cc2,
      admin1_code,
      admin2_code,
      admin3_code,
      admin4_code,
      population,
      elevation,
      dem,
      timezone,
      modification_date
    FROM (
      SELECT * FROM places_ww
        WHERE feature_class = \'P\'
        AND population > 0
      UNION
      SELECT * FROM places_us
        WHERE feature_class = \'P\'
        AND population > 0
    ) a
  )
');
echo "SUCCESS!!\n";


echo 'Adding spatial index ... ';
// Populate the shape column
$dbInstaller->run('UPDATE geoname SET shape =
    ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)::GEOGRAPHY');
echo "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Load country and admin region tables
// ----------------------------------------------------------------------

echo 'Loading administrative region data ... ';
$dbInstaller->copyFrom($download_path . 'admin1CodesASCII.txt',
    'admin1_codes_ascii');
echo "SUCCESS!!\n";


echo 'Loading country data ... ';
// Replace '#' prefixed comments from flat files
replaceComments($download_path . 'countryInfo.txt');
$dbInstaller->copyFrom($download_path . 'countryInfo.txt', 'country_info');
echo "SUCCESS!!\n";



// ----------------------------------------------------------------------
// Geoserve data clean-up
// ----------------------------------------------------------------------

print 'Cleaning up downloaded data ... ';
$downloads = scandir($download_path);
foreach ($downloads as $download) {
  if (!is_dir($download)) {
    unlink($download_path . $download);
  }
}
rmdir($download_path);
print "SUCCESS!!\n";

?>
