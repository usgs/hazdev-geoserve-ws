<?php

include_once 'DatabaseInstaller.class.php';


/**
 * PostgreSQL implementation of DatabaseInstaller.
 */
class PostgresDatabaseInstaller extends DatabaseInstaller {

  // database name, parsed from $url.
  private $dbname;

  /**
   * Called by DatabaseInstaller::getInstaller().
   */
  protected function __construct ($url, $user, $pass) {
    parent::__construct($url, $user, $pass);
    preg_match('/dbname=([^;]+)/', $url, $matches);
    if (count($matches) < 2) {
      throw new Exception('"dbname" is required in a postgress connection url');
    }
    $this->dbname = $matches[1];
  }

  /**
   * Check if database exists.
   *
   * @return {Boolean} true if able to connect to database, false otherwise.
   */
  public function databaseExists () {
    try {
      $this->connect();
      $this->dbh = null;
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  /**
   * Drop the database referred to by $url.
   */
  public function dropDatabase () {
    $this->disconnect();
    $db = $this->connectWithoutDbname();
    $db->exec('DROP DATABASE IF EXISTS ' . $this->dbname);
    $db = null;
  }

  /**
   * Create the database referred to by $url.
   */
  public function createDatabase () {
    $db = $this->connectWithoutDbname();
    $db->exec('CREATE DATABASE ' . $this->dbname);
    $db = null;
    // enable postgis
    $this->enablePostgis();
  }

  /**
   * Disable postgis extension
   */
  public function disablePostgis () {
    $this->run('DROP EXTENSION IF EXISTS postgis');
  }

  /**
   * Enable postgis extension
   */
  public function enablePostgis () {
    $this->run('CREATE EXTENSION postgis');
  }

  /**
   * Drop $user with roles
   */
  public function dropUser ($roles, $user) {
    if ($this->userExists($user)) {
      $this->revokeRoles($roles, $user);
      $this->run('DROP USER IF EXISTS ' . $user);
    }
  }

  /**
   * Create user with $roles
   */
  public function createUser ($roles, $user, $password) {
    // drop user if it already exists
    $this->dropUser($roles, $user);
    // create read only user
    $this->run('CREATE USER ' . $user . ' WITH PASSWORD \'' . $password . '\'');
    $this->grantRoles($roles, $user);
  }

  /**
   * Grant all $roles to $user
   */
  public function grantRoles ($roles, $user) {
    $this->run('GRANT USAGE ON SCHEMA public TO ' . $user);
    $this->run('GRANT ' . implode(',', $roles) .
        ' ON ALL TABLES IN SCHEMA public TO ' . $user);
  }

  /**
   * Revoke all $roles to $user
   */
  public function revokeRoles ($roles, $user) {
    $this->run('REVOKE USAGE ON SCHEMA public FROM ' . $user);
    $this->run('REVOKE GRANT OPTION FOR ' . implode(',', $roles) .
        ' ON ALL TABLES IN SCHEMA public FROM ' . $user);
    $this->run('REVOKE ALL PRIVILEGES ON DATABASE ' . $this->dbname .
        ' FROM ' . $user);
  }

  /**
   * Checks if $user exists
   */
  public function userExists ($user) {
    $db = $this->connectWithoutDbname();
    $sql = 'select usename from pg_catalog.pg_user where usename=\'' .
        $user . '\'';
    $result = $db->query($sql)->fetchColumn();
    $db = null;

    if ($result === false) {
      return false;
    }

    // $user exists
    return true;
  }

  /**
   * Loads table with data from flat file
   *
   * @param  $file  {String}, absolute path to data file
   * @param  $table {String}, table to copy data into.
   */
  public function copyFrom ($file, $table, $options=array('NULL AS \'\'')) {
    $this->run('COPY ' . $table . ' FROM \'' . $file . '\' ' .
        implode(' ', $options));
  }

  /**
   * Connect to the mysql server without specifying a database name.
   * Used by dropDatabase() and createDatabase().
   */
  protected function connectWithoutDbname() {
    $db = new PDO(str_replace('dbname=' . $this->dbname, 'dbname=postgres',
        $this->url), $this->user, $this->pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $db;
  }

}
