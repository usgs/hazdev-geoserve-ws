Getting Started
===============

Dependencies
------------

Details provided in this document assume one is working on MacOS with Homebrew.
Instructions must be adapted for other environments.

- PHP
- Node
- Docker

### PHP (5.5 or later)

```
$ brew tap homebrew/php
$ brew install php55 --with-cgi --with-postgresql
```

### Node (8.9 or later)

```
$ brew install nvm
$ nvm install lts/*
```

### Docker (17.05 or later)

[https://docs.docker.com/engine/installation/](https://docs.docker.com/engine/installation/)


Local Development
-----------------

### Database

The application requires a connection to a PostgreSQL database with PostGIS
enabled. One may use any such available database. If no such database is
available, a docker instance will suffice. See the [README.md file](README.md)
for details on setting this up.

### Web Server
```
$ export DB_PASS=postgres
$ export DB_DSN='pgsql:host={{HOSTNAME}};port=5432;dbname=earthquake;'
$ export DB_SCHEMA=geoserve
$ export DB_USER=postgres
$ npm run build
$ npm start
```

The previous step should have started a local development server listening on
port 9040. This should now be accessible in a browser at:

```
  http://localhost:9040/
```

Files may be edited in `src/htdocs` and results may be viewed by refreshing
the browser.
