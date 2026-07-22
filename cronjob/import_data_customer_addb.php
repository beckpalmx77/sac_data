<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");

$sql_keymax = " select ADDB_KEY from addrbook order by ADDB_KEY desc  limit 1  ";
$statement = $conn->query($sql_keymax);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $result) {

    $ADDB_KEY_LAST = $result['ADDB_KEY'];

}

$sql_sqlsvr = "select * from addrbook  where ADDB_KEY >= " . $ADDB_KEY_LAST;


//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM addrbook WHERE ADDB_KEY = :ADDB_KEY");
$stmt_update = $conn->prepare("UPDATE addrbook SET ADDB_COMPANY=:ADDB_COMPANY,ADDB_TAX_ID=:ADDB_TAX_ID,ADDB_SEARCH=:ADDB_SEARCH,ADDB_BRANCH=:ADDB_BRANCH,ADDB_ADDB_1=:ADDB_ADDB_1,ADDB_ADDB_2=:ADDB_ADDB_2,ADDB_ADDB_3=:ADDB_ADDB_3,ADDB_PROVINCE=:ADDB_PROVINCE,ADDB_PHONE=:ADDB_PHONE WHERE ADDB_KEY = :ADDB_KEY");
$stmt_insert = $conn->prepare("INSERT INTO addrbook(ADDB_KEY,ADDB_COMPANY,ADDB_TAX_ID,ADDB_SEARCH,ADDB_BRANCH,ADDB_ADDB_1,ADDB_ADDB_2,ADDB_ADDB_3,ADDB_PROVINCE,ADDB_PHONE) VALUES (:ADDB_KEY,:ADDB_COMPANY,:ADDB_TAX_ID,:ADDB_SEARCH,:ADDB_BRANCH,:ADDB_ADDB_1,:ADDB_ADDB_2,:ADDB_ADDB_3,:ADDB_PROVINCE,:ADDB_PHONE)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([':ADDB_KEY' => $result_sqlsvr["ADDB_KEY"]]);
        if ($stmt_find->fetchColumn() > 0) {
            $stmt_update->execute([
                ':ADDB_COMPANY' => $result_sqlsvr["ADDB_COMPANY"],
                ':ADDB_TAX_ID' => $result_sqlsvr["ADDB_TAX_ID"],
                ':ADDB_SEARCH' => $result_sqlsvr["ADDB_SEARCH"],
                ':ADDB_BRANCH' => $result_sqlsvr["ADDB_BRANCH"],
                ':ADDB_ADDB_1' => $result_sqlsvr["ADDB_ADDB_1"],
                ':ADDB_ADDB_2' => $result_sqlsvr["ADDB_ADDB_2"],
                ':ADDB_ADDB_3' => $result_sqlsvr["ADDB_ADDB_3"],
                ':ADDB_PROVINCE' => $result_sqlsvr["ADDB_PROVINCE"],
                ':ADDB_PHONE' => $result_sqlsvr["ADDB_PHONE"],
                ':ADDB_KEY' => $result_sqlsvr["ADDB_KEY"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':ADDB_KEY' => $result_sqlsvr["ADDB_KEY"],
                ':ADDB_COMPANY' => $result_sqlsvr["ADDB_COMPANY"],
                ':ADDB_TAX_ID' => $result_sqlsvr["ADDB_TAX_ID"],
                ':ADDB_SEARCH' => $result_sqlsvr["ADDB_SEARCH"],
                ':ADDB_BRANCH' => $result_sqlsvr["ADDB_BRANCH"],
                ':ADDB_ADDB_1' => $result_sqlsvr["ADDB_ADDB_1"],
                ':ADDB_ADDB_2' => $result_sqlsvr["ADDB_ADDB_2"],
                ':ADDB_ADDB_3' => $result_sqlsvr["ADDB_ADDB_3"],
                ':ADDB_PROVINCE' => $result_sqlsvr["ADDB_PROVINCE"],
                ':ADDB_PHONE' => $result_sqlsvr["ADDB_PHONE"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "customer_addb import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in customer_addb import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


