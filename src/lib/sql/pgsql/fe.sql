------------------------------------------------------------------------
-- FE REGIONS - DROP
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS fe_shape_index;
DROP INDEX IF EXISTS fe_rename_shape_index;

/* Views */
DROP VIEW IF EXISTS fe_all_view;

/* Tables */
DROP TABLE IF EXISTS fe CASCADE;
DROP TABLE IF EXISTS fe_rename CASCADE;


------------------------------------------------------------------------------
-- FE REGIONS - CREATE
------------------------------------------------------------------------------

/* Tables */
CREATE TABLE fe (
  id     INTEGER PRIMARY KEY,
  num    INTEGER,
  place  VARCHAR(100),
  shape  GEOMETRY(GEOMETRY, 4326)
);

CREATE TABLE fe_rename (
  id     INTEGER PRIMARY KEY,
  place  VARCHAR(100),
  shape  GEOMETRY(GEOMETRY, 4326)
);

/* Indexes */
CREATE INDEX fe_shape_index ON fe USING GIST (shape);
CREATE INDEX fe_rename_shape_index ON fe_rename USING GIST (shape);

/* Views */
CREATE VIEW fe_view AS (
  SELECT
    fe.id,
    fe.num,
    fe.place,
    fe.shape,
    2 AS priority,
    'fe'::text AS dataset
  FROM fe

  UNION ALL

  SELECT
    fe_rename.id,
    NULL::DECIMAL AS num,
    fe_rename.place,
    fe_rename.shape,
    1 AS priority,
    'fe_rename'::text AS dataset
  FROM fe_rename
);
