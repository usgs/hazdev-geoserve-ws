<?php


$data_directory = $APP_DIR . '/lib/data';

// Cities
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

$dbInstaller->copyFrom($data_directory . '/geonames/cities1000.txt', 'places_ww');

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

$dbInstaller->copyFrom($data_directory . '/geonames/US.txt', 'places_us');

// Load/Merge data into geoname table
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
$dbInstaller->run('UPDATE geoname SET shape = ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)::GEOGRAPHY');

?>