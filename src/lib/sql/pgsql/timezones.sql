------------------------------------------------------------------------
-- DROP - TIMEZONE
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS timezones_shape_index;

/* Tables */
DROP TABLE IF EXISTS timezones CASCADE;


------------------------------------------------------------------------------
-- CREATE - ADMIN REGION
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE timezones (
  id             INT PRIMARY KEY,
  timezone       VARCHAR(255),
  dststart       VARCHAR(20),
  dstend         VARCHAR(20),
  standardoffset int,
  dstoffset      int,
  geometry       GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX timezones_shape_index ON timezones USING GIST (geometry);
