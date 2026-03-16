<?php

include('db_value_sqlserver.inc');

try {
    $conn_sqlsvr = new PDO("sqlsrv:server=$host ; Database = $dbname", $dbuser, $dbpass);
    $conn_sqlsvr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo $e->getMessage();
}
