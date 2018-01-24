#!/usr/bin/env bash

# The existence of this file indicates the data load is complete.
# Serves as a proxy for whether the database container is healthy
touch /var/lib/postgresql/data/pgdata/.data-load-complete
