<?php 

if(!defined('jsurlshort') ) exit('Invalid entry point.');//protect file from roge calls
// db options
define('DB_NAME', 'XXXXXX');
define('DB_USER', 'XXXXXX');
define('DB_PASSWORD', 'XXXXXX');
define('DB_HOST', 'localhost');
define('DB_TABLE', 'urlshort');

// connect to database
$db = new PDO("mysql:host=DB_HOST;dbname=DB_NAME", DB_USER, DB_PASSWORD);

// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/' );