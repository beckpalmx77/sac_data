<?php

define('DB_HOST','localhost');
define('DB_USER','sadmin');
define('DB_PASS','sadmin');
define('DB_NAME','myadmin_dbs');

try
{
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
    exit("Error: " . $e->getMessage());
}
?>