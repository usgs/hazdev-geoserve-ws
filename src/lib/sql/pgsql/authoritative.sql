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
  name      VARCHAR(255),
  type      VARCHAR(255),
  priority  INTEGER,
  network   VARCHAR(255),
  region    VARCHAR(255),
  url       VARCHAR(255),
  shape     GEOMETRY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX authoritative_shape_index ON authoritative USING GIST (shape);
