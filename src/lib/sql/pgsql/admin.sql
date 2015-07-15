------------------------------------------------------------------------
-- DROP - ADMIN REGION
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS admin_shape_index;

/* Tables */
DROP TABLE IF EXISTS admin CASCADE;


------------------------------------------------------------------------------
-- CREATE - ADMIN REGION
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE admin (
  id       INTEGER PRIMARY KEY,
  iso      CHAR(3),
  country  VARCHAR(100),
  region   VARCHAR(100),
  shape    GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX admin_shape_index ON admin USING GIST (shape);
