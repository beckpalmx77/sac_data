<?php
date_default_timezone_set("Asia/Bangkok");
include('db_value2.inc');

try
{
    $conn2 = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";port=" .DB_PORT,DB_USER, DB_PASS
        ,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET AUTOCOMMIT=1"));
    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn2->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
    exit("Error: " . $e->getMessage());
}