------------------------------------------------------------------------
-- DROP - FE OFFSHORE
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS offshore_shape_index;

/* Tables */
DROP TABLE IF EXISTS offshore CASCADE;


------------------------------------------------------------------------------
-- CREATE - FE OFFSHORE
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE offshore (
  id        INTEGER PRIMARY KEY,
  name      VARCHAR(255),
  shape     GEOMETRY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX offshore_shape_index ON offshore USING GIST (shape);
