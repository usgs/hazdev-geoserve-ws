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
