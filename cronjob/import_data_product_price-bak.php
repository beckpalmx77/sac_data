<?php

ini_set('display_errors', 1);
error_reporting(~0);

include(dirname(__DIR__) . "/config/connect_sqlserver.php");
include(dirname(__DIR__) . "/config/connect_db.php");
include(dirname(__DIR__) . "/config/connect_db2s.php");
include(dirname(__DIR__) . "/cond_file/query-product-price-main.php");

$sql_sqlsvr = $select_query . $sql_cond . $sql_order;

//$myfile = fopen("sqlqry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

echo "กำลังอ่านข้อมูลจาก MSSQL...\n";
$all_rows = $stmt_sqlsvr->fetchAll(PDO::FETCH_ASSOC);
$stmt_sqlsvr->closeCursor();
$total_rows = count($all_rows);
echo "พบข้อมูลทั้งหมด: $total_rows รายการ\n";

if ($total_rows == 0) {
    echo "ไม่พบข้อมูลในการนำเข้า\n";
    exit;
}

$count_insert = 0;
$count_insert2 = 0;
$count_update = 0;
$count_update2 = 0;
$current = 0;
$batch_size = 500;

echo "กำลังนำเข้าข้อมูล...\n";
ob_implicit_flush(true);

$conn->beginTransaction();
$conn2->beginTransaction();

foreach ($all_rows as $result_sqlsvr) {
    $current++;
    
    if ($current % 1 == 0 || $current == $total_rows) {
        echo "\r[{$current}/{$total_rows}] DB1:I:{$count_insert} U:{$count_update} | DB2:I:{$count_insert2} U:{$count_update2} ";
        @ob_flush();
        flush();
    }

    $sql = "REPLACE INTO ims_product(product_key,product_id,pgroup_id,name_t,brand_id,price_code,price,unit_name) 
            VALUES (:product_key,:product_id,:pgroup_id,:name_t,:brand_id,:price_code,:price,:unit_name)";
    $query = $conn->prepare($sql);
    $query->bindParam(':product_key', $result_sqlsvr["SKU_KEY"], PDO::PARAM_STR);
    $query->bindParam(':product_id', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
    $query->bindParam(':pgroup_id', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
    $query->bindParam(':name_t', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
    $query->bindParam(':brand_id', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
    $query->bindParam(':price_code', $result_sqlsvr["ARPRB_CODE"], PDO::PARAM_STR);
    $query->bindParam(':price', $result_sqlsvr["ARPLU_U_PRC"], PDO::PARAM_STR);
    $query->bindParam(':unit_name', $result_sqlsvr["UTQ_NAME"], PDO::PARAM_STR);
    $query->execute();

    $affected = $query->rowCount();
    if ($affected > 0) {
        if ($affected == 1) {
            $count_insert++;
        } else {
            $count_update++;
        }
    }

    $query2 = $conn2->prepare($sql);
    $query2->bindParam(':product_key', $result_sqlsvr["SKU_KEY"], PDO::PARAM_STR);
    $query2->bindParam(':product_id', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
    $query2->bindParam(':pgroup_id', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
    $query2->bindParam(':name_t', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
    $query2->bindParam(':brand_id', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
    $query2->bindParam(':price_code', $result_sqlsvr["ARPRB_CODE"], PDO::PARAM_STR);
    $query2->bindParam(':price', $result_sqlsvr["ARPLU_U_PRC"], PDO::PARAM_STR);
    $query2->bindParam(':unit_name', $result_sqlsvr["UTQ_NAME"], PDO::PARAM_STR);
    $query2->execute();

    $affected2 = $query2->rowCount();
    if ($affected2 > 0) {
        if ($affected2 == 1) {
            $count_insert2++;
        } else {
            $count_update2++;
        }
    }

    // Commit ทุก batch_size รายการ
    if ($current % $batch_size == 0) {
        $conn->commit();
        $conn2->commit();
        $conn->beginTransaction();
        $conn2->beginTransaction();
    }
}

// Commit ส่วนที่เหลือ
$conn->commit();
$conn2->commit();

echo "\n";
echo "=== สรุปผลการทำงาน ===\n";
echo "อ่านข้อมูลจาก MSSQL: $total_rows รายการ\n";
echo "อัพเดท DB1 (sac_data): $count_update รายการ\n";
echo "อัพเดท DB2 (sac_data2): $count_update2 รายการ\n";
echo "เพิ่มใหม่ DB1 (sac_data): $count_insert รายการ\n";
echo "เพิ่มใหม่ DB2 (sac_data2): $count_insert2 รายการ\n";
echo "เสร็จสิ้นการทำงาน\n";

$conn_sqlsvr = null;
$conn = null;
$conn2 = null;

