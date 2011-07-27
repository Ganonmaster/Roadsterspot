<?php

$dbms = 'mysql';
$dbserv = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'roadsterspot';
$dbport = false;

$subdir = '/roadsterspot/';

$root_url = 'http://' . $_SERVER['HTTP_HOST'] . $subdir;
define('ROOT_URL', $root_url);
	
define("MAPS_HOST", "maps.google.com");
define("MAPS_KEY", "");

define('DEBUG', false);
define('DEBUG_EXTRA', false);