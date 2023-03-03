<?php
date_default_timezone_set('Asia/Bangkok');

$filename = "Data_Customer-" . date('m/d/Y H:i:s', time()) . ".csv";

@header('Content-type: text/csv; charset=UTF-8');
@header('Content-Encoding: UTF-8');
@header("Content-Disposition: attachment; filename=" . $filename);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");

include ("../cond_file/doc_info_customer_ar.php");

$sql_sqlsvr = $select_query ;

//$my_file = fopen("D-sac_str1.txt", "w") or die("Unable to open file!");
//fwrite($my_file, $String_Sql);
//fclose($my_file);

$data = "AR_CODE,AR_NAME,ADDB_PROVINCE\n";

$query = $conn_sqlsvr->prepare($sql_sqlsvr);
$query->execute();

$loop = 0;

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $loop++;
    $data .= $loop . ",";
    $data .= str_replace(",", "^", $row['AR_CODE']) . ",";
    $data .= str_replace(",", "^", $row['AR_NAME']) . ",";
    $data .= str_replace(",", "^", $row['ADDB_PROVINCE']) . "\n";


}

$data = iconv("utf-8", "tis-620", $data);
echo $data;

exit();