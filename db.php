<?php

// ENV CONST
const DBHOST = 'localhost';
const DBUSER = 'root';
const DBPASS = '';
const DBNAME = 'localement_suisse';

// DSN CONNECTION
$dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;

try {
    // INSTANCE PDO
    $db = new PDO($dsn, DBUSER, DBPASS);

    // DB IN UTF8
    $db->exec("set names utf8");
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $exception) {
    // STOP CODE THEN DISPLAY ERROR
    die($exception->getMessage());
}