#!/usr/bin/env bash
set -e

# Run data loader stuff
if [ -z "${APP_INSTALLER}" ]; then
  APP_INSTALLER='/hazdev-project/lib/pre-install.php';
fi

if [ -z "${DB_LOAD_TYPE}" ]; then
  # Should be set by environment variable, if not, default incremental
  DB_LOAD_TYPE='incremental';
fi

php ${APP_INSTALLER} --non-interactive --${DB_LOAD_TYPE};
