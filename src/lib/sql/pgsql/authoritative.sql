------------------------------------------------------------------------
-- DROP - AUTHORITATIVE NETWORK
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS authoritative_shape_index;

/* Tables */
DROP TABLE IF EXISTS authoritative CASCADE;


------------------------------------------------------------------------------
-- CREATE - AUTHORITATIVE NETWORK
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE authoritative (
  id        INTEGER PRIMARY KEY,
  name      VARCHAR(10),
  type      VARCHAR(50),
  priority  INTEGER,
  network   VARCHAR(10),
  shape     GEOGRAPHY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX authoritative_shape_index ON authoritative USING GIST (shape);
