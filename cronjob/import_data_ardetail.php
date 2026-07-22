<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");


$sql_keymax = " select ARD_KEY from ardetail order by ARD_KEY desc  limit 1  ";
$statement = $conn->query($sql_keymax);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $result) {

    $ARD_KEY_LAST = $result['ARD_KEY'];

}


$sql_sqlsvr = "select * from ardetail where ARD_KEY >= " . $ARD_KEY_LAST;

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ardetail WHERE ARD_KEY = :ARD_KEY");
$stmt_update = $conn->prepare("UPDATE ardetail SET ARD_AR=:ARD_AR,ARD_DI=:ARD_DI,ARD_ARCD=:ARD_ARCD WHERE ARD_KEY = :ARD_KEY");
$stmt_insert = $conn->prepare("INSERT INTO ardetail(ARD_KEY,ARD_AR,ARD_DI,ARD_ARCD) VALUES (:ARD_KEY,:ARD_AR,:ARD_DI,:ARD_ARCD)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([':ARD_KEY' => $result_sqlsvr["ARD_KEY"]]);
        $nRows = $stmt_find->fetchColumn();

        if ($nRows > 0) {
            $stmt_update->execute([
                ':ARD_AR' => $result_sqlsvr["ARD_AR"],
                ':ARD_DI' => $result_sqlsvr["ARD_DI"],
                ':ARD_ARCD' => $result_sqlsvr["ARD_ARCD"],
                ':ARD_KEY' => $result_sqlsvr["ARD_KEY"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':ARD_KEY' => $result_sqlsvr["ARD_KEY"],
                ':ARD_AR' => $result_sqlsvr["ARD_AR"],
                ':ARD_DI' => $result_sqlsvr["ARD_DI"],
                ':ARD_ARCD' => $result_sqlsvr["ARD_ARCD"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "ardetail import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in ardetail import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


