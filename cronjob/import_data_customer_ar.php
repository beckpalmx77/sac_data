<?php

ini_set('display_errors', 1);
error_reporting(~0);

$start_time = microtime(true);

include(dirname(__DIR__) . "/config/connect_sqlserver.php");
include(dirname(__DIR__) . "/config/connect_db.php");
include(dirname(__DIR__) . "/cond_file/doc_info_customer_ar.php");

$sql_sqlsvr = $select_query
            . $sql_cond
            . $sql_order;

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

// Check and ensure index exists on customer_id for high performance lookups/updates
$stmt_idx = $conn->query("SHOW INDEX FROM ims_customer_ar WHERE Key_name = 'idx_customer_id'");
if (count($stmt_idx->fetchAll()) == 0) {
    try {
        $conn->exec("ALTER TABLE ims_customer_ar ADD INDEX idx_customer_id (customer_id)");
    } catch (PDOException $e) {
        // Ignore if index creation fails or exists
    }
}

// Pre-fetch all existing customer_ids into memory to eliminate N+1 SELECT queries
$existing_ids = $conn->query("SELECT customer_id FROM ims_customer_ar WHERE customer_id IS NOT NULL AND customer_id != ''")->fetchAll(PDO::FETCH_COLUMN);
$existing_map = array_flip($existing_ids);

$stmt_update = $conn->prepare("UPDATE ims_customer_ar SET tax_id=:tax_id,f_name=:f_name,credit=:credit,phone=:phone,address=:address,tumbol=:tumbol,amphure=:amphure,province=:province,zipcode=:zipcode,ARCD_NAME=:ARCD_NAME,sale_name=:sale_name,contact_name=:contact_name,ADDB_KEY=:ADDB_KEY,ADDB_BRANCH=:ADDB_BRANCH,price_code=:ARCD_ARPRBCODE WHERE customer_id = :customer_id");
$stmt_insert = $conn->prepare("INSERT INTO ims_customer_ar(customer_id,tax_id,f_name,credit,phone,address,tumbol,amphure,province,zipcode,ARCD_NAME,sale_name,contact_name,ADDB_KEY,ADDB_BRANCH,price_code) VALUES (:customer_id,:tax_id,:f_name,:credit,:phone,:address,:tumbol,:amphure,:province,:zipcode,:ARCD_NAME,:sale_name,:contact_name,:ADDB_KEY,:ADDB_BRANCH,:ARCD_ARPRBCODE)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $ar_code = $result_sqlsvr["AR_CODE"];
        $contact_name = $result_sqlsvr["CT_INTL"] . " " . $result_sqlsvr["CT_NAME"] . " " . $result_sqlsvr["CT_SURNME"] . " - " . $result_sqlsvr["CT_JOBTITLE"];

        if (isset($existing_map[$ar_code])) {
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
                ':customer_id' => $ar_code
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':customer_id' => $ar_code,
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
            $existing_map[$ar_code] = true;
        }
    }
    $conn->commit();
    $elapsed = round(microtime(true) - $start_time, 3);
    echo "customer_ar import finished. Insert: $count_insert, Update: $count_update (Time: {$elapsed} s)\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in customer_ar import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;
