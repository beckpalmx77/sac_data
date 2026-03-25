<?php

ini_set('display_errors', 1);
error_reporting(~0);

include(dirname(__DIR__) . "/config/connect_sqlserver.php");
include(dirname(__DIR__) . "/config/connect_db.php");
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

echo "กำลังนำเข้าข้อมูล...\n";

$batch_size = 100;
$current = 0;
$values_db1 = [];
$values_db2 = [];

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

    $values_db1[] = "('$product_key','$product_id','$pgroup_id','$name_t','$brand_id','$price_code','$price','$unit_name')";
    $values_db2[] = "('$product_key','$product_id','$pgroup_id','$name_t','$brand_id','$price_code','$price','$unit_name')";

    if ($current % $batch_size == 0 || $current == $total_rows) {
        $values_str_db1 = implode(",", $values_db1);
        $values_str_db2 = implode(",", $values_db2);

        $sql = "INSERT INTO ims_product (product_key,product_id,pgroup_id,name_t,brand_id,price_code,price,unit_name) 
                VALUES $values_str_db1
                ON DUPLICATE KEY UPDATE 
                    product_key = VALUES(product_key),
                    price = VALUES(price),
                    unit_name = VALUES(unit_name)";
        $conn->exec($sql);

        $sql2 = "INSERT INTO ims_product (product_key,product_id,pgroup_id,name_t,brand_id,price_code,price,unit_name) 
                VALUES $values_str_db2
                ON DUPLICATE KEY UPDATE 
                    product_key = VALUES(product_key),
                    price = VALUES(price),
                    unit_name = VALUES(unit_name)";
        $conn2->exec($sql2);

        echo "\r[{$current}/{$total_rows}] ";
        @ob_flush();
        flush();

        $values_db1 = [];
        $values_db2 = [];
    }
}

echo "\n";
echo "=== สรุปผลการทำงาน ===\n";
echo "อ่านข้อมูลจาก MSSQL: $total_rows รายการ\n";
echo "เสร็จสิ้นการทำงาน\n";

$conn_sqlsvr = null;
$conn = null;
$conn2 = null;

