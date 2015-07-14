------------------------------------------------------------------------
-- DROP - GEONAMES PLACES
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS country_info_iso_index;
DROP INDEX IF EXISTS admin1_codes_ascii_code_index;
DROP INDEX IF EXISTS geoname_shape_index;
DROP INDEX IF EXISTS geoname_population_index;
DROP INDEX IF EXISTS geoname_feature_class_index;

/* Tables */
DROP TABLE IF EXISTS admin1_codes_ascii CASCADE;
DROP TABLE IF EXISTS country_info CASCADE;
DROP TABLE IF EXISTS geoname CASCADE;


------------------------------------------------------------------------------
-- CREATE - GEONAMES PLACES
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
