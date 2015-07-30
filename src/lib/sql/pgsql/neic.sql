------------------------------------------------------------------------
-- NEIC REGIONS - DROP
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS neic_catalog_shape_index;
DROP INDEX IF EXISTS neic_response_shape_index;

/* Tables */
DROP TABLE IF EXISTS neic_catalog CASCADE;
DROP TABLE IF EXISTS neic_response CASCADE;


------------------------------------------------------------------------
-- NEIC REGIONS - Create
------------------------------------------------------------------------

/* Tables */
CREATE TABLE neic_catalog (
  id INTEGER PRIMARY KEY,
  name VARCHAR(50),
  magnitude DECIMAL(2, 1),
  shape GEOGRAPHY(GEOMETRY, 4326),
  type VARCHAR(50)
);

CREATE TABLE neic_response (
  id INTEGER PRIMARY KEY,
  name VARCHAR(50),
  magnitude DECIMAL(2, 1),
  shape GEOGRAPHY(GEOMETRY, 4326),
  type VARCHAR(50)
);

/* Indexes */
CREATE INDEX neic_catalog_shape_index ON neic_catalog USING GIST (shape);
CREATE INDEX neic_response_shape_index ON neic_response USING GIST (shape);
