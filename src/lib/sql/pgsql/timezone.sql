------------------------------------------------------------------------
-- DROP - TIMEZONE
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS timezone_shape_index;

/* Tables */
DROP TABLE IF EXISTS timezone CASCADE;


------------------------------------------------------------------------------
-- CREATE - ADMIN REGION
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE timezone (
  id             INT PRIMARY KEY,
  timezone       VARCHAR(255),
  dststart       VARCHAR(20),
  dstend         VARCHAR(20),
  standardoffset int,
  dstoffset      int,
  shape          GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX timezone_shape_index ON timezone USING GIST (shape);
