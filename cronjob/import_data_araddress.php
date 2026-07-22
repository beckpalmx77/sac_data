<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");


$sql_keymax = " select ARA_KEY from araddress order by ARA_KEY desc  limit 1  ";
$statement = $conn->query($sql_keymax);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $result) {

    $ARA_KEY_LAST = $result['ARA_KEY'];

}

$sql_sqlsvr = "select * from araddress  where ARA_KEY >= " . $ARA_KEY_LAST;

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM araddress WHERE ARA_KEY = :ARA_KEY");
$stmt_update = $conn->prepare("UPDATE araddress SET ARA_AR=:ARA_AR,ARA_ADDB=:ARA_ADDB,ARA_DEFAULT=:ARA_DEFAULT,ARA_LASTUPD=:ARA_LASTUPD WHERE ARA_KEY = :ARA_KEY");
$stmt_insert = $conn->prepare("INSERT INTO araddress(ARA_KEY,ARA_AR,ARA_ADDB,ARA_DEFAULT,ARA_LASTUPD) VALUES (:ARA_KEY,:ARA_AR,:ARA_ADDB,:ARA_DEFAULT,:ARA_LASTUPD)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([':ARA_KEY' => $result_sqlsvr["ARA_KEY"]]);
        $nRows = $stmt_find->fetchColumn();

        if ($nRows > 0) {
            $stmt_update->execute([
                ':ARA_AR' => $result_sqlsvr["ARA_AR"],
                ':ARA_ADDB' => $result_sqlsvr["ARA_ADDB"],
                ':ARA_DEFAULT' => $result_sqlsvr["ARA_DEFAULT"],
                ':ARA_LASTUPD' => $result_sqlsvr["ARA_LASTUPD"],
                ':ARA_KEY' => $result_sqlsvr["ARA_KEY"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':ARA_KEY' => $result_sqlsvr["ARA_KEY"],
                ':ARA_AR' => $result_sqlsvr["ARA_AR"],
                ':ARA_ADDB' => $result_sqlsvr["ARA_ADDB"],
                ':ARA_DEFAULT' => $result_sqlsvr["ARA_DEFAULT"],
                ':ARA_LASTUPD' => $result_sqlsvr["ARA_LASTUPD"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "araddress import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in araddress import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


