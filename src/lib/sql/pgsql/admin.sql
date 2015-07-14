------------------------------------------------------------------------
-- DROP - ADMIN REGION
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS globaladmin_shape_index;

/* Tables */
DROP TABLE IF EXISTS globaladmin CASCADE;


------------------------------------------------------------------------------
-- CREATE - ADMIN REGION
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
