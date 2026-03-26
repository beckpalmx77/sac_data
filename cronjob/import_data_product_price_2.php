<?php

ini_set('display_errors', 1);
error_reporting(~0);

include(dirname(__DIR__) . "/config/connect_sqlserver.php");
include(dirname(__DIR__) . "/config/connect_db2s.php");
include(dirname(__DIR__) . "/cond_file/query-product-price-main.php");

$sql_sqlsvr = $select_query . $sql_cond . $sql_order;

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

echo "กำลังนำเข้าข้อมูล (sac_data2)...\n";

$batch_size = 1000;
$current = 0;
$values_db2 = [];

$insert_count = 0;
$error_count2 = 0;

foreach ($all_rows as $result_sqlsvr) {
    $current++;
    
    $product_key = $result_sqlsvr["SKU_KEY"];
    $product_id = $result_sqlsvr["SKU_CODE"];
    $pgroup_id = $result_sqlsvr["ICCAT_CODE"];
    $name_t = str_replace(["'", "\\"], ["''", "\\\\"], $result_sqlsvr["SKU_NAME"]);
    $brand_id = $result_sqlsvr["BRN_CODE"];
    $price_code = $result_sqlsvr["ARPRB_CODE"];
    $price = $result_sqlsvr["ARPLU_U_PRC"];
    $unit_name = $result_sqlsvr["UTQ_NAME"];

    $values_db2[] = "('$product_key','$product_id','$pgroup_id','$name_t','$brand_id','$price_code','$price','$unit_name')";

    if ($current % $batch_size == 0 || $current == $total_rows) {
        $values_str_db2 = implode(",", $values_db2);

        try {
            $sql2 = "INSERT INTO ims_product (product_key,product_id,pgroup_id,name_t,brand_id,price_code,price,unit_name) 
                    VALUES $values_str_db2
                    ON DUPLICATE KEY UPDATE 
                        product_key = VALUES(product_key),
                        pgroup_id = VALUES(pgroup_id),
                        name_t = VALUES(name_t),
                        brand_id = VALUES(brand_id),
                        price_code = VALUES(price_code),
                        price = VALUES(price),
                        unit_name = VALUES(unit_name)";
            $conn2->exec($sql2);

        } catch (PDOException $e) {
            $error_count2 += count($values_db2);
            error_log("Error importing to sac_data2: " . $e->getMessage());
            echo "Error sac_data2: " . $e->getMessage() . "\n";
        }

        $insert_update_rows = count($values_db2);
        if ($current <= $batch_size) {
            $insert_count = $insert_update_rows;
        } else {
            $insert_count += $insert_update_rows;
        }

        echo "\r[{$current}/{$total_rows}] ";

        $values_db2 = [];
    }
}

echo "\n";
echo "=== สรุปผลการทำงาน ===\n";
echo "อ่านข้อมูลจาก MSSQL: $total_rows รายการ\n";
echo "นำเข้าข้อมูล: $insert_count รายการ\n";
echo "เกิดข้อผิดพลาด sac_data2: $error_count2 รายการ\n";

echo "sac_data2 count: " . $conn2->query("SELECT COUNT(*) FROM ims_product")->fetchColumn() . "\n";

$conn_sqlsvr = null;
$conn2 = null;

echo "เสร็จสิ้นการทำงาน\n";
