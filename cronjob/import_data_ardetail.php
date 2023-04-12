<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");

$sql_sqlsvr = "select * from ardetail ";

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {


    $sql_find = "SELECT * FROM ardetail WHERE ARD_KEY = '" . $result_sqlsvr["ARD_KEY"] . "'";
    $nRows = $conn->query($sql_find)->fetchColumn();
    if ($nRows > 0) {
        $sql = "UPDATE ardetail SET ARD_AR=:ARD_AR,ARD_DI=:ARD_DI,ARD_ARCD=:ARD_ARCD        
        WHERE ARD_KEY = :ARD_KEY ";

        echo " Update Customer : " . $result_sqlsvr["ARD_KEY"] . " | " . $result_sqlsvr["ARD_AR"] . " | " . $result_sqlsvr["ARD_ARCD"] . "\n\r";

        $query = $conn->prepare($sql);
        $query->bindParam(':ARD_AR', $result_sqlsvr["ARD_AR"], PDO::PARAM_STR);
        $query->bindParam(':ARD_DI', $result_sqlsvr["ARD_DI"], PDO::PARAM_STR);
        $query->bindParam(':ARD_ARCD', $result_sqlsvr["ARD_ARCD"], PDO::PARAM_STR);
        $query->bindParam(':ARD_KEY', $result_sqlsvr["ARD_KEY"], PDO::PARAM_STR);
        $query->execute();
    } else {

        echo " Insert Customer : " . $result_sqlsvr["ARD_KEY"] . " | " . $result_sqlsvr["ARD_AR"] . " | " . $result_sqlsvr["ARD_ARCD"] . "\n\r";

        $sql = "INSERT INTO ardetail(ARD_KEY,ARD_AR,ARD_DI,ARD_ARCD)
        VALUES (:ARD_KEY,:ARD_AR,:ARD_DI,:ARD_ARCD)";
        $query = $conn->prepare($sql);
        $query->bindParam(':ARD_KEY', $result_sqlsvr["ARD_KEY"], PDO::PARAM_STR);
        $query->bindParam(':ARD_AR', $result_sqlsvr["ARD_AR"], PDO::PARAM_STR);
        $query->bindParam(':ARD_DI', $result_sqlsvr["ARD_DI"], PDO::PARAM_STR);
        $query->bindParam(':ARD_ARCD', $result_sqlsvr["ARD_ARCD"], PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $conn->lastInsertId();

        if ($lastInsertId) {
            echo "Save OK";
        } else {
            echo "Error";
        }

/*
        $return_arr[] = array("customer_id" => $result_sqlsvr['AR_CODE'],
            "tax_id" => $result_sqlsvr['ARD_DI'],
            "f_name" => $result_sqlsvr['AR_NAME'],
            "phone" => $result_sqlsvr['ADDB_PHONE'],
            "address" => $result_sqlsvr['ADDB_ADDB_1'],
            "tumbol" => $result_sqlsvr['ADDB_ADDB_2'],
            "amphure" => $result_sqlsvr['ADDB_ADDB_3'],
            "province" => $result_sqlsvr['ADDB_PROVINCE'],
            "zipcode" => $result_sqlsvr['ADDB_POST']);
*/
    }
/*
    $customer_data = json_encode($return_arr);
    file_put_contents("customer_data.json", $customer_data);
    echo json_encode($return_arr);
*/

}

$conn_sqlsvr=null;

