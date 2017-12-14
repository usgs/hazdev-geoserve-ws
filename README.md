# hazdev-geoserve-ws
A web service that takes latitude and longitude values and returns
geographically relevant information. Developed for use by the U.S.
Geological Survey.

 - [Getting Started](GettingStarted.md)
 - [About Datasets](Data.md)



## Docker image

This project includes a `Dockerfile` that can be used to build a container.

> NOTE: this project uses multi-stage builds, and requires `Docker v17.05.0-ce (2017-05-04)` or newer to build a container image.


**To build the container**

```
cd hazdev-geoserve-ws
docker build -t usgs/hazdev-geoserve-ws:latest .
```


**To run the container**

Replace `{{VARIABLES}}` with appropriate values in the following example command:

```
docker run --name=geoserve -d -p {{HOSTPORT}}:80 \
  -e 'DB_DSN=pgsql:host={{HOSTNAME}};port=5432;dbname={{DBNAME}}' \
  -e 'DB_USER={{READONLY_USERNAME}}' \
  -e 'DB_PASS={{READONLY_PASSWORD}}' \
  -e 'DB_SCHEMA={{DBSCHEMA}}' \
  usgs/hazdev-geoserve-ws:latest
```

- `{{DBNAME}}` - postgres database name
- `{{DBSCHEMA}}` - database schema, if geoserve tables are not in the public schema
- `{{HOSTNAME}}` - database server hostname
- `{{HOSTPORT}}` - external port to expose
- `{{READONLY_USERNAME}}` - database user with read access to tables
- `{{READONLY_PASSWORD}}` - password for database user


Then access the interface by visiting `http://localhost:{{HOSTPORT}}/ws/geoserve/`


** Data loading from the container **

Because data is only loaded, and not updated, it's also possible to run the geoserve database in a container.

```
## configuration

# temporary data load directory
DATALOAD_DIR=/tmp/dataload
# persistent data directory (so container can be replaced)
PGDATA_DIR=`pwd`/pgdata
# postgres user database password
POSTGRES_PASSWORD=changethis
# port to expose
HOSTPORT=8080
# read only database username and password
DB_USER=web
DB_PASS=webpass
# database naming
DB_SCHEMA=geoserve
DB_NAME=geoserve


## create database container
docker run --name=geoservedb -d \
    -v "${DATALOAD_DIR}:/tmp/dataload" \
    -v "${PGDATA_DIR}:/var/lib/postgresql/data" \
    -e "POSTGRES_PASSWORD=${POSTGRES_PASSWORD}" \
    mdillon/postgis


## load data

docker run --rm -it \
    --link geoservedb:geoservedb \
    -e "DB_ROOT_DSN=pgsql:host=geoservedb;port=5432;dbname=${DB_NAME}" \
    -e "DB_USER=${DB_USER}" \
    -e "DB_PASS=${DB_PASS}" \
    -e "DB_SCHEMA=${DB_SCHEMA}" \
    -e "DOWNLOAD_DIR=/tmp/dataload" \
    -v "${DATALOAD_DIR}:/tmp/dataload" \
    usgs/hazdev-geoserve-ws:latest \
    /usr/bin/php /var/www/apps/hazdev-geoserve-ws/lib/pre-install.php

# Previous configuration file found
#   choose [1] Use previous configuration
# Database adminstrator DSN (accept default)
# Database administrator user (accept default)
# Database adminstrator password
#   {{POSTGRES_PASSWORD}}
# Follow prompts to load data, create {{READONLY_USERNAME}} user, etc.


## create geoserve container (listening on port 8080 in this example)

docker run --name=geoserve -d -p 8080:80 \
    --link geoservedb:geoservedb \
    -e "DB_DSN=pgsql:host=geoservedb;port=5432;dbname=${DB_NAME}" \
    -e "DB_USER=${DB_USER}" \
    -e "DB_PASS=${DB_PASS}" \
    -e "DB_SCHEMA=${DB_SCHEMA}" \
    usgs/hazdev-geoserve-ws:latest
```
