# hazdev-geoserve-ws
A web service that takes latitude and longitude values and returns
geographically relevant information. Developed for use by the U.S.
Geological Survey.

 - [Getting Started](GettingStarted.md)
 - [About Datasets](Data.md)



## Docker image

This project includes `Dockerfile`s that can be used to build containers.

This project also includes a `docker-compose.yml` file that can be used for local development running in containers:
```
docker-compose up
```


> NOTE: this project uses multi-stage builds, and requires
> `Docker v17.05.0-ce (2017-05-04)` or newer to build a container image.


**To build the containers**

```
cd hazdev-geoserve-ws
docker build -t usgs/hazdev-geoserve-db:latest -f Dockerfile-db .
docker build -t usgs/hazdev-geoserve-ws:latest -f Dockerfile-ws .
```

You  may optionally specify a few `--build-arg` switches to customize the build.

- `BUILD_IMAGE` The name of the Docker image to use in the first stage of
                creating the image.
- `FROM_IMAGE`  The name of the Docker image to use in the second stage of
                creating the image.


**To run the containers**

The following examples demonstrate environment variables used by the containers:

```
# start database container
# data load may take a while on first run

docker run --rm --name=geoservedb -d \
    -e POSTGRES_PASSWORD=changethis \
    -e PGDATA=/var/lib/postgresql/data/pgdata \
    -v $(pwd)/data/pgdata:/var/lib/postgresqsl/data/pgdata \
    -e 'DB_ADMIN_DSN=pgsql:dbname=earthquake' \
    -e DB_ADMIN_USER=postgres \
    -e DB_ADMIN_PASSWORD=changethis \
    -e DB_LOAD_TYPE=incremental \
    -e DB_NAME=earthquake \
    -e DB_SCHEMA=geoserve \
    -e DB_USER=web \
    -e DB_PASS=alsochangethis \
    usgs/hazdev-geoserve-db:latest

# start webservice container
docker run --rm --name=geoserve -d \
    -p 8080:80 \
    --link geoservedb:geoservedb \
    -e 'DB_DSN=pgsql:host=geoservedb;port=5432;dbname=earthquake' \
    -e DB_USER=web \
    -e DB_PASS=alsochangethis \
    -e DB_SCHEMA=geoserve \
    usgs/hazdev-geoserve-ws:latest
```

Once the containers are started, access the interface by visiting `http://localhost:8080/ws/geoserve/`
