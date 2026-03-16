<?php

ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");
include("../config/connect_db2s.php");
include("../cond_file/query-product-price-main.php");

$sql_sqlsvr = $select_query . $sql_cond . $sql_order;

//$myfile = fopen("sqlqry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

echo "กำลังอ่านข้อมูลจาก MSSQL...\n";
$all_rows = $stmt_sqlsvr->fetchAll(PDO::FETCH_ASSOC);
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

echo "กำลังนำเข้าข้อมูล...\n";

foreach ($all_rows as $result_sqlsvr) {
    $current++;
    
    if ($current % 100 == 0 || $current == $total_rows) {
        echo "\r[{$current}/{$total_rows}] กำลังประมวลผล... ";
    }

    $sql_find = "SELECT * FROM ims_product WHERE product_id = '" . $result_sqlsvr["SKU_CODE"] ."'"
        . " AND product_key = '" . $result_sqlsvr["SKU_KEY"] . "'"
        . " AND price_code = '" . $result_sqlsvr["ARPRB_CODE"] . "'";

    //$myfile = fopen("myqeury_file_find.txt", "w") or die("Unable to open file!");
    //fwrite($myfile, $sql_find);
    //fclose($myfile);

    $nRows = $conn->query($sql_find)->fetchColumn();
    if ($nRows > 0) {
        $sql = "UPDATE ims_product SET name_t=:name_t , brand_id=:brand_id , pgroup_id=:pgroup_id , price=:price "
            . " WHERE product_id = '" . $result_sqlsvr["SKU_CODE"] . "'"
            . " AND product_key = '" . $result_sqlsvr["SKU_KEY"] . "'"
            . " AND price_code = '" . $result_sqlsvr["ARPRB_CODE"] . "'";
        $query = $conn->prepare($sql);
        $query->bindParam(':name_t', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query->bindParam(':brand_id', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':pgroup_id', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query->bindParam(':price', $result_sqlsvr["ARPLU_U_PRC"], PDO::PARAM_STR);
        $query->execute();
        $count_update++;

        $query2 = $conn2->prepare($sql);
        $query2->bindParam(':name_t', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query2->bindParam(':brand_id', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
        $query2->bindParam(':pgroup_id', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query2->bindParam(':price', $result_sqlsvr["ARPLU_U_PRC"], PDO::PARAM_STR);
        $query2->execute();
        $count_update2++;

    } else {

        $sql = "INSERT INTO ims_product(product_key,product_id,pgroup_id,name_t,brand_id,price_code,price) 
                VALUES (:product_key,:product_id,:pgroup_id,:name_t,:brand_id,:price_code,:price)";
        $query = $conn->prepare($sql);
        $query->bindParam(':product_key', $result_sqlsvr["SKU_KEY"], PDO::PARAM_STR);
        $query->bindParam(':product_id', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
        $query->bindParam(':pgroup_id', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query->bindParam(':name_t', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query->bindParam(':brand_id', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':price_code', $result_sqlsvr["ARPRB_CODE"], PDO::PARAM_STR);
        $query->bindParam(':price', $result_sqlsvr["ARPLU_U_PRC"], PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $conn->lastInsertId();

        if ($lastInsertId) {
            $count_insert++;

            $query2 = $conn2->prepare($sql);
            $query2->bindParam(':product_key', $result_sqlsvr["SKU_KEY"], PDO::PARAM_STR);
            $query2->bindParam(':product_id', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
            $query2->bindParam(':pgroup_id', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
            $query2->bindParam(':name_t', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
            $query2->bindParam(':brand_id', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
            $query2->bindParam(':price_code', $result_sqlsvr["ARPRB_CODE"], PDO::PARAM_STR);
            $query2->bindParam(':price', $result_sqlsvr["ARPLU_U_PRC"], PDO::PARAM_STR);
            $query2->execute();

            $lastInsertId2 = $conn2->lastInsertId();
            if ($lastInsertId2) {
                $count_insert2++;
            }
        }

    }
}

echo "\n";
echo "=== สรุปผลการทำงาน ===\n";
echo "อ่านข้อมูลจาก MSSQL: $total_rows รายการ\n";
echo "อัพเดท DB1 (sac_data): $count_update รายการ\n";
echo "อัพเดท DB2 (sac_data2): $count_update2 รายการ\n";
echo "เพิ่มใหม่ DB1 (sac_data): $count_insert รายการ\n";
echo "เพิ่มใหม่ DB2 (sac_data2): $count_insert2 รายการ\n";
echo "เสร็จสิ้นการทำงาน\n";

$conn_sqlsvr = null;

