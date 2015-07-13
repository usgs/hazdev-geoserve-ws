/*
 * SQL containing all of the "create" statements for the geoserve database
 */

------------------------------------------------------------------------------
-- AUTHORITATIVE NETWORK
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE authoritative (
  id INTEGER PRIMARY KEY,
  name VARCHAR(10),
  priority INTEGER,
  network VARCHAR(10),
  shape GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX authoritative_shape_index ON authoritative USING GIST (shape);


------------------------------------------------------------------------------
-- FE REGIONS
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE fe (
  id     INTEGER PRIMARY KEY,
  num    INTEGER,
  place  VARCHAR(100),
  shape  GEOGRAPHY(GEOMETRY, 4326)
);

CREATE TABLE fe_rename (
  id     INTEGER PRIMARY KEY,
  place  VARCHAR(100),
  shape  GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX fe_shape_index ON fe USING GIST (shape);
CREATE INDEX fe_rename_shape_index ON fe_rename USING GIST (shape);

/* Views */
CREATE VIEW fe_view AS (
  SELECT
    fe.id,
    fe.num,
    fe.place,
    fe.shape,
    2 AS priority,
    'fe'::text AS dataset
  FROM fe

  UNION ALL

  SELECT
    fe_rename.id,
    NULL::DECIMAL AS num,
    fe_rename.place,
    fe_rename.shape,
    1 AS priority,
    'fe_rename'::text AS dataset
  FROM fe_rename
);


------------------------------------------------------------------------------
-- GEONAME PLACES
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE geoname (
    geoname_id         INTEGER PRIMARY KEY,
    name               VARCHAR(200),
    ascii_name         VARCHAR(200),
    alternate_names    TEXT,
    latitude           DECIMAL,
    longitude          DECIMAL,
    feature_class      CHAR(1),
    feature_code       VARCHAR(10),
    country_code       CHAR(2),
    cc2                VARCHAR(60),
    admin1_code        VARCHAR(20),
    admin2_code        VARCHAR(80),
    admin3_code        VARCHAR(20),
    admin4_code        VARCHAR(20),
    population         BIGINT,
    elevation          INTEGER,
    dem                INTEGER,
    timezone           VARCHAR(40),
    modification_date  DATE,
    shape              GEOGRAPHY(POINT, 4326)
);

CREATE TABLE country_info (
    iso                   CHAR(2) PRIMARY KEY,
    iso3                  CHAR(3),
    iso_numeric           INTEGER,
    fips                  VARCHAR(3),
    country               VARCHAR(200),
    capital               VARCHAR(200),
    area_km               DECIMAL,
    population            INTEGER,
    continent             CHAR(2),
    tld                   CHAR(10),
    currency_code         CHAR(3),
    currency_name         CHAR(15),
    phone                 VARCHAR(20),
    postal_code           VARCHAR(60),
    postal_regex          VARCHAR(200),
    languages             VARCHAR(200),
    geoname_id            INTEGER,
    neighbours            VARCHAR(50),
    equivalent_fips_code  VARCHAR(3)
);

CREATE TABLE admin1_codes_ascii (
    code        CHAR(20) PRIMARY KEY,
    name        TEXT,
    name_ascii  TEXT,
    geoname_id  INTEGER
);


/* Indexes */
CREATE INDEX geoname_shape_index ON geoname USING GIST (shape);
CREATE INDEX geoname_population_index ON geoname (population);
CREATE INDEX geoname_feature_class_index ON geoname (feature_class);
CREATE INDEX country_info_iso_index on country_info (iso);
CREATE INDEX admin1_codes_ascii_code_index on admin1_codes_ascii (code);


------------------------------------------------------------------------------
-- REGION COUNTRY/STATE
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE globaladmin (
  id       INTEGER PRIMARY KEY,
  iso      CHAR(3),
  country  VARCHAR(100),
  region   VARCHAR(100),
  shape    GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX globaladmin_shape_index ON globaladmin USING GIST (shape);


------------------------------------------------------------------------------
-- TECTONIC SUMMARY
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE tectonic_summary_region (
    objectid   INTEGER PRIMARY KEY,
    area       DECIMAL,
    perimeter  DECIMAL,
    name       VARCHAR(50),
    text       TEXT,
    shape      GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX tectonic_summary_region_shape_index ON tectonic_summary_region
    USING GIST (shape);
