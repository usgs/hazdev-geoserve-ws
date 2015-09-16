------------------------------------------------------------------------
-- DROP - TECTONCIC SUMMARY
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS tectonic_summary_shape_index;

/* Tables */
DROP TABLE IF EXISTS tectonic_summary CASCADE;


------------------------------------------------------------------------------
-- CREATE - ADMIN REGION
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE tectonic_summary (
  id       INTEGER PRIMARY KEY,
  name     VARCHAR(255),
  summary  text,
  type     VARCHAR(255),
  shape    GEOGRAPHY(GEOMETRY, 4326)

);

/* Indexes */
CREATE INDEX tectonic_summary_shape_index ON
  tectonic_summary USING GIST (shape);
