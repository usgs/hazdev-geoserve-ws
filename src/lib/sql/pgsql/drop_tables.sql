/*
 * SQL containing all of the "drop" statements for the geoserve database
 */

------------------------------------------------------------------------
-- AUTHORITATIVE NETWORK
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS authoritative_shape_index;

/* Tables */
DROP TABLE IF EXISTS authoritative CASCADE;


------------------------------------------------------------------------
-- FE REGIONS
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS fe_shape_index;
DROP INDEX IF EXISTS fe_rename_shape_index;

/* Views */
DROP VIEW IF EXISTS fe_all_view;

/* Tables */
DROP TABLE IF EXISTS fe CASCADE;
DROP TABLE IF EXISTS fe_rename CASCADE;


------------------------------------------------------------------------
-- GEONAME PLACES
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS country_info_iso_index;
DROP INDEX IF EXISTS admin1_codes_ascii_code_index;
DROP INDEX IF EXISTS geoname_shape_index;
DROP INDEX IF EXISTS geoname_population_index;
DROP INDEX IF EXISTS geoname_feature_class_index;

/* Tables */
DROP TABLE IF EXISTS admin1_codes_ascii CASCADE;
DROP TABLE IF EXISTS country_info CASCADE;
DROP TABLE IF EXISTS geoname CASCADE;


------------------------------------------------------------------------
-- REGION COUNTRY/STATE
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS globaladmin_shape_index;

/* Tables */
DROP TABLE IF EXISTS globaladmin CASCADE;


------------------------------------------------------------------------
-- TECTONIC SUMMARY
------------------------------------------------------------------------

/* Indexes */
DROP INDEX IF EXISTS tectonic_summary_region_shape_index;

/* Tables */
DROP TABLE IF EXISTS tectonic_summary_region CASCADE;
