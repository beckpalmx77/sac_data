<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../cond_file/doc_info-query-001.php");

$doc_id = 'BKSV256503/1054';

$year = date("Y");
$month = date("m");

echo "Year = " . $year ;
echo "\n\r";
echo "Month = " . $month ;
echo "\n\r";

$sql = $select_query . $sql_cond . " AND DI_REF = '" . $doc_id . "'" . $sql_order ;

$myfile = fopen("qry_file2.txt", "w") or die("Unable to open file!");
fwrite($myfile, $sql);
fclose($myfile);

$stmt = $conn->prepare($sql);
$stmt->execute();


while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $result["DI_KEY"];
}
