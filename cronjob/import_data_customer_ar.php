<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");

include ("../cond_file/doc_info_customer_ar.php");

$sql_sqlsvr = $select_query
            //. $sql_cond . " AND AR_CODE like 'SAC%' "
            . $sql_cond
            . $sql_order ;

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_customer_ar WHERE customer_id = :customer_id");
$stmt_update = $conn->prepare("UPDATE ims_customer_ar SET tax_id=:tax_id,f_name=:f_name,credit=:credit,phone=:phone,address=:address,tumbol=:tumbol,amphure=:amphure,province=:province,zipcode=:zipcode,ARCD_NAME=:ARCD_NAME,sale_name=:sale_name,contact_name=:contact_name,ADDB_KEY=:ADDB_KEY,ADDB_BRANCH=:ADDB_BRANCH,price_code=:ARCD_ARPRBCODE WHERE customer_id = :customer_id");
$stmt_insert = $conn->prepare("INSERT INTO ims_customer_ar(customer_id,tax_id,f_name,credit,phone,address,tumbol,amphure,province,zipcode,ARCD_NAME,sale_name,contact_name,ADDB_KEY,ADDB_BRANCH,price_code) VALUES (:customer_id,:tax_id,:f_name,:credit,:phone,:address,:tumbol,:amphure,:province,:zipcode,:ARCD_NAME,:sale_name,:contact_name,:ADDB_KEY,:ADDB_BRANCH,:ARCD_ARPRBCODE)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $contact_name = $result_sqlsvr["CT_INTL"] . " " . $result_sqlsvr["CT_NAME"] . " " . $result_sqlsvr["CT_SURNME"] . " - " . $result_sqlsvr["CT_JOBTITLE"];

        $stmt_find->execute([':customer_id' => $result_sqlsvr["AR_CODE"]]);
        if ($stmt_find->fetchColumn() > 0) {
            $stmt_update->execute([
                ':tax_id' => $result_sqlsvr["ADDB_TAX_ID"],
                ':f_name' => $result_sqlsvr["AR_NAME"],
                ':credit' => $result_sqlsvr["ARS_CRE_LIM"],
                ':phone' => $result_sqlsvr["ADDB_PHONE"],
                ':address' => $result_sqlsvr["ADDB_ADDB_1"],
                ':tumbol' => $result_sqlsvr["ADDB_ADDB_2"],
                ':amphure' => $result_sqlsvr["ADDB_ADDB_3"],
                ':province' => $result_sqlsvr["ADDB_PROVINCE"],
                ':zipcode' => $result_sqlsvr["ADDB_POST"],
                ':ARCD_NAME' => $result_sqlsvr["ARCD_NAME"],
                ':sale_name' => $result_sqlsvr["SLMN_NAME"],
                ':contact_name' => $contact_name,
                ':ADDB_KEY' => $result_sqlsvr["ADDB_KEY"],
                ':ADDB_BRANCH' => $result_sqlsvr["ADDB_BRANCH"],
                ':ARCD_ARPRBCODE' => $result_sqlsvr["ARCD_ARPRBCODE"],
                ':customer_id' => $result_sqlsvr["AR_CODE"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':customer_id' => $result_sqlsvr["AR_CODE"],
                ':tax_id' => $result_sqlsvr["ADDB_TAX_ID"],
                ':f_name' => $result_sqlsvr["AR_NAME"],
                ':credit' => $result_sqlsvr["ARS_CRE_LIM"],
                ':phone' => $result_sqlsvr["ADDB_PHONE"],
                ':address' => $result_sqlsvr["ADDB_ADDB_1"],
                ':tumbol' => $result_sqlsvr["ADDB_ADDB_2"],
                ':amphure' => $result_sqlsvr["ADDB_ADDB_3"],
                ':province' => $result_sqlsvr["ADDB_PROVINCE"],
                ':zipcode' => $result_sqlsvr["ADDB_POST"],
                ':ARCD_NAME' => $result_sqlsvr["ARCD_NAME"],
                ':sale_name' => $result_sqlsvr["SLMN_NAME"],
                ':contact_name' => $contact_name,
                ':ADDB_KEY' => $result_sqlsvr["ADDB_KEY"],
                ':ADDB_BRANCH' => $result_sqlsvr["ADDB_BRANCH"],
                ':ARCD_ARPRBCODE' => $result_sqlsvr["ARCD_ARPRBCODE"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "customer_ar import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in customer_ar import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


