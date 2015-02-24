<?php

// ----------------------------------------------------------------------
// Geonames data download/uncompress
// ----------------------------------------------------------------------

// TODO:: prompt user to download geoname data (cities1000.zip, US.zip)
$answer = promptYesNo("\nWould you like to download and load geonames data " .
    "into the schema",'Y');

if (!$answer) {
  print "Normal exit.\n";
  exit(0);
}

$answer = promptYesNo("\nThe schema must already exist in order to " .
    "load geonames data, continue",'N');

if (!$answer) {
  print "Normal exit.\n";
  exit(0);
}

// download geoname data
$url = configure('GEONAMES_URL', 'http://download.geonames.org/export/dump/',
    "\nGeonames download url ");
$filenames = array('cities1000.zip', 'US.zip', 'admin1CodesASCII.txt',
    'countryInfo.txt');
$download_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'geonames'
    . DIRECTORY_SEPARATOR;

// create temp directory
mkdir($download_path);

foreach ($filenames as $filename) {
  $downloaded_file = $download_path . $filename;
  downloadURL($url . $filename, $downloaded_file);

  // uncompress geonames data
  if (pathinfo($downloaded_file)['extension'] === 'zip') {
    print "\nExtract file: " . $downloaded_file . "\n\n";
    extractZip($downloaded_file, $download_path);
  }
}

// ----------------------------------------------------------------------
// Remove geonames data from tables
// ----------------------------------------------------------------------

// Delete from geoname
$dbInstaller->run('DELETE FROM geoname');

// Delete from geoname
$dbInstaller->run('DELETE FROM admin1_codes_ascii');

// Delete from geoname
$dbInstaller->run('DELETE FROM country_info');


// ----------------------------------------------------------------------
// Genames data load into temp tables
// ----------------------------------------------------------------------

// Cities

print "\nLoading Cities1000 data ... ";
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
print "SUCCESS!!\n";

print "Loading US cities data ... ";
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
print "SUCCESS!!\n";



// ----------------------------------------------------------------------
// Genames data load from temp tables into schema
// ----------------------------------------------------------------------

// Load/Merge data into geoname table
print "Inserting geonames data into database (approx 2 minutes) ... ";
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
      SELECT * FROM places_ww where feature_class = \'P\'
      UNION
      SELECT * FROM places_us where feature_class = \'P\'
    ) a
  )
');

// Populate the shape column
$dbInstaller->run('UPDATE geoname SET shape =
    ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)::GEOGRAPHY');
print "SUCCESS!!\n";


// ----------------------------------------------------------------------
// Load country and admin region tables
// ----------------------------------------------------------------------

print "Loading administrative region data ... ";
$dbInstaller->copyFrom($download_path . 'admin1CodesASCII.txt',
    'admin1_codes_ascii');
print "SUCCESS!!\n";


print "Loading country data ... ";
// Replace '#' prefixed comments from flat files
replaceComments($download_path . 'countryInfo.txt');
$dbInstaller->copyFrom($download_path . 'countryInfo.txt', 'country_info');
print "SUCCESS!!\n";



// ----------------------------------------------------------------------
// Geoserve data clean-up
// ----------------------------------------------------------------------

print "Cleaning up downloaded data ... ";
$downloads = scandir($download_path);
foreach ($downloads as $download) {
  if (!is_dir($download)) {
    unlink($download_path . $download);
  }
}
rmdir($download_path);
print "SUCCESS!!\n\n";

?>