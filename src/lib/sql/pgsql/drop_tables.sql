/*
 * SQL containing all of the "drop" statements for the geoserve database
 */

------------------------------------------------------------------------
-- AUTHORITATIVE NETWORK
------------------------------------------------------------------------

/* Tables */
DROP TABLE IF EXISTS authoritative_region CASCADE;
DROP TABLE IF EXISTS authoritative_region_us CASCADE;

/* Indexes */
DROP INDEX IF EXISTS authoritative_region_shape_index;
DROP INDEX IF EXISTS authoritative_region_us_shape_index;


------------------------------------------------------------------------
-- FE REGIONS
------------------------------------------------------------------------

/* Views */
DROP VIEW IF EXISTS fe_all_view;

/* Tables */
DROP TABLE IF EXISTS fe CASCADE;
DROP TABLE IF EXISTS fe_plus CASCADE;
DROP TABLE IF EXISTS fe_rename CASCADE;

/* Indexes */
DROP INDEX IF EXISTS fe_shape_index;
DROP INDEX IF EXISTS fe_plus_shape_index;
DROP INDEX IF EXISTS fe_rename_shape_index;


------------------------------------------------------------------------
-- GEONAME PLACES
------------------------------------------------------------------------

/* Tables */
DROP TABLE IF EXISTS geoname CASCADE;

/* Indexes */
DROP INDEX IF EXISTS geoname_shape_index;
DROP INDEX IF EXISTS geoname_population_index;
DROP INDEX IF EXISTS geoname_feature_class_index;


------------------------------------------------------------------------
-- REGION COUNTRY/STATE
------------------------------------------------------------------------

/* Tables */
DROP TABLE IF EXISTS country CASCADE;
DROP TABLE IF EXISTS state CASCADE;

/* Indexes */
DROP INDEX IF EXISTS country_shape_index;
DROP INDEX IF EXISTS state_shape_index;


------------------------------------------------------------------------
-- TECTONIC SUMMARY
------------------------------------------------------------------------

/* Tables */
DROP TABLE IF EXISTS tectonic_summary_region CASCADE;

/* Indexes */
DROP INDEX IF EXISTS tectonic_summary_region_shape_index;
