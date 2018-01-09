<?php

if (preg_match('/\.json$/', $_SERVER['SCRIPT_NAME'])) {
  // Web service request. Set format=json in GET and serve from .php file
  $_GET['format'] = 'json';

  require $_SERVER['DOCUMENT_ROOT'] .
      str_replace('.json', '.php', $_SERVER['SCRIPT_NAME']);
  exit;
} else {
  // Not a service request, serve matched file
  return false;
}
