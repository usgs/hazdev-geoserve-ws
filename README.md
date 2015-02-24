# hazdev-geoserve-ws
A web service that takes latitude and longitude values and returns
geographically relevant information. Developed for use by the U.S.
Geological Survey.

## Getting Started
We recommend using `homebrew` to install application dependencies.

### Dependencies

There are multiple dependencies that must be installed for this project:

1. PHP
1. PostgreSQL
1. PostGIS
1. NPM Dependencies
1. Sass and Compass
1. Grunt

#### Install PHP

    brew install php55

#### Install PostgreSQL

This will take you through the process of installing, starting, and creating, a
postgreSQL database locally.

1. Brew Install

        brew install postgresql

    After running `brew install postgresql`, the terminal will output directions
    that you will use to get your installation up and running.

1. Create/Upgrade a Database

    If this is your first install, create a database with:

        /usr/local/Cellar/postgresql/9.4.1/bin/initdb /usr/local/var/postgres

    You will be able to copy and past this command directly from your terminal.
    Copy the command from your terminal and **not from this example**. You
    should find the command at the end of the output after running `brew
    install postgresql`. 

    > Note: If you do not have permissions on `/usr/local/var/postgres`, then
    > you can define a different directory. We suggest defining a `.tmp`
    > directory at the root level of this application.

1. Start/Stop PostgreSQL

    After running `/usr/local/Cellar/postgresql/9.4.1/bin/initdb <directory>`,
    you should see a sucess message. Below the success message there will be a
    couple commands.  Go ahead and run the next command.

        /usr/local/Cellar/postgresql/9.4.1/bin/postgres -D /usr/local/var/postgres

    > Note: Once again if you do not have permissions on
    > `/usr/local/var/postgres`, then you can define a different directory. We
    > suggest defining a `.tmp` directory at the root level of this application.

1. Login

    Login to the default `postgres` database with the user that created the
    database. 

        psql postgres

    > Note: PostgreSQL will create the default database `postgres`, which you
    > can access with the same user that you used to create the database. If the
    > database server successfully started you may login using the command,
    > `psql postgres <username>`


#### Install PostGIS

PostGIS adds spatial functions that are required for this application.

    brew install postgis

#### Install NPM Dependencies

    npm install

#### Install Sass and Compass with Ruby

    gem install sass
    gem install compass

#### Preview in a Browser

    grunt

To view the application after running grunt, go to the URL
`http://localhost:8100/index.php`

## Having trouble getting started?

If this is your first time using **grunt**, you need to install the grunt
command line interface globally

    npm install -g grunt-cli

