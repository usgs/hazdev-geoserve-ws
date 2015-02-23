/*
 * SQL containing all of the "create" statements for the geoserve database
 */

------------------------------------------------------------------------------
-- AUTHORITATIVE NETWORK
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE authoritative_region (
    objectid          INTEGER PRIMARY KEY,
    area              DECIMAL,
    perimeter         DECIMAL,
    aeic_c_id         INTEGER,
    name              VARCHAR(50),
    flag              VARCHAR(5),
    priority_num      INTEGER,
    name_add          VARCHAR(50),
    alt_name          VARCHAR(50),
    cent_lat          DECIMAL,
    cent_lon          DECIMAL,
    se_anno_cad_data  BYTEA,
    auth_region_or    DECIMAL,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

CREATE TABLE authoritative_region_us (
    objectid          INTEGER PRIMARY KEY,
    area              DECIMAL,
    perimeter         DECIMAL,
    id                INTEGER,
    name              VARCHAR(50),
    priority_num      INTEGER,
    se_anno_cad_data  BYTEA,
    auth_region_or    DECIMAL,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX authoritative_region_shape_index ON authoritative_region
    USING GIST (GEOGRAPHY(shape));
CREATE INDEX authoritative_region_us_shape_index ON authoritative_region_us
    USING GIST (GEOGRAPHY(shape));


------------------------------------------------------------------------------
-- FE REGIONS
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE fe (
    objectid_1        INTEGER PRIMARY KEY,
    objectid          INTEGER,
    fe_reg_num        DECIMAL,
    fe_geom_ar        DECIMAL,
    fe_geom_le        DECIMAL,
    name_s            VARCHAR(100),
    name_m            VARCHAR(100),
    name_e            VARCHAR(100),
    name_l            VARCHAR(100),
    name_h            VARCHAR(100),
    area              DECIMAL,
    len               DECIMAL,
    se_anno_cad_data  BYTEA,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

CREATE TABLE fe_plus (
    objectid_1        INTEGER PRIMARY KEY,
    objectid          INTEGER,
    fepl_disso        INTEGER,
    name_s            VARCHAR(50),
    fep_or            DECIMAL,
    name_e            VARCHAR(100),
    name_l            VARCHAR(100),
    name_h            VARCHAR(100),
    name_m            VARCHAR(100),
    time_code         VARCHAR(20),
    shape_leng        DECIMAL,
    area              DECIMAL,
    se_anno_cad_data  BYTEA,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

CREATE TABLE fe_rename (
    objectid_1        INTEGER PRIMARY KEY,
    objectid          INTEGER,
    name_m            VARCHAR(100),
    name_s            VARCHAR(100),
    name_e            VARCHAR(100),
    name_l            VARCHAR(100),
    name_h            VARCHAR(100),
    area              DECIMAL,
    len               DECIMAL,
    se_anno_cad_data  BYTEA,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX fe_shape_index ON fe USING GIST (GEOGRAPHY(shape));
CREATE INDEX fe_plus_shape_index ON fe_plus USING GIST (GEOGRAPHY(shape));
CREATE INDEX fe_rename_shape_index ON fe_rename USING GIST (GEOGRAPHY(shape));

/* Views */
CREATE VIEW fe_view AS (
  SELECT
    fe.objectid_1 AS objectid,
    fe.name_s,
    fe.name_m,
    fe.name_e,
    fe.name_h,
    fe.name_l,
    fe.area,
    fe.shape,
    fe.fe_reg_num,
    3 AS priority,
    'fe'::text AS dataset
  FROM fe

  UNION ALL

  SELECT
    fe_rename.objectid_1 AS objectid,
    fe_rename.name_s,
    fe_rename.name_m,
    fe_rename.name_e,
    fe_rename.name_h,
    fe_rename.name_l,
    fe_rename.area,
    fe_rename.shape,
    NULL::DECIMAL AS fe_reg_num,
    2 AS priority,
    'fe_rename'::text AS dataset
  FROM fe_rename

  UNION ALL

  SELECT
    fe_plus.objectid_1 AS objectid,
    fe_plus.name_s,
    fe_plus.name_m,
    fe_plus.name_e,
    fe_plus.name_h,
    fe_plus.name_l,
    fe_plus.area,
    fe_plus.shape,
    NULL::DECIMAL AS fe_reg_num,
    1 AS priority,
    'fe_plus'::text AS dataset
  FROM fe_plus
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
    iso                   CHAR(2),
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
    geoname_id            INTEGER PRIMARY KEY,
    neighbours            VARCHAR(50),
    equivalent_fips_code  VARCHAR(3)
);

CREATE TABLE admin1_codes_ascii (
    code        CHAR(20),
    name        TEXT,
    name_ascii  TEXT,
    geoname_id  INTEGER PRIMARY KEY
);


/* Indexes */
CREATE INDEX geoname_shape_index ON geoname USING GIST (GEOGRAPHY(shape));
CREATE INDEX geoname_population_index ON geoname (population);
CREATE INDEX geoname_feature_class_index ON geoname (feature_class);
CREATE INDEX country_info_iso_index on country_info (iso);
CREATE INDEX admin1_codes_ascii_code_index on admin1_codes_ascii (code);


------------------------------------------------------------------------------
-- REGION COUNTRY/STATE
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE country (
    objectid          INTEGER PRIMARY KEY,
    fips_cntry        VARCHAR(2),
    gmi_cntry         VARCHAR(3),
    iso_2digit        VARCHAR(2),
    iso_3digit        VARCHAR(3),
    cntry_name        VARCHAR(40),
    long_name         VARCHAR(40),
    sovereign         VARCHAR(40),
    pop_cntry         INTEGER,
    curr_type         VARCHAR(16),
    curr_code         VARCHAR(4),
    landlocked        VARCHAR(1),
    sqkm              DECIMAL,
    sqmi              DECIMAL,
    color_map         VARCHAR(1),
    se_anno_cad_data  BYTEA,
    min_lat           INTEGER,
    max_lat           INTEGER,
    min_lon           INTEGER,
    max_lon           INTEGER,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

CREATE TABLE state (
    objectid          INTEGER PRIMARY KEY,
    name              VARCHAR(90),
    x_cen             DECIMAL,
    y_cen             DECIMAL,
    se_anno_cad_data  BYTEA,
    shape             GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX country_shape_index ON country USING GIST (GEOGRAPHY(shape));
CREATE INDEX state_shape_index ON state USING GIST (GEOGRAPHY(shape));


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
    USING GIST (GEOGRAPHY(shape));
