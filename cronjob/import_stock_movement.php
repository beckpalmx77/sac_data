<?php
ini_set('display_errors', 1);
error_reporting(~0);
set_time_limit(0);
ini_set('memory_limit', '1024M');

include('../config/connect_db.php');
include('../config/connect_sqlserver.php');
include('../cond_file/query_product_stock.php');

$sql_main = $select_query_stock . $sql_cond_stock . $sql_group_stock . $sql_order_stock;

$create_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");

echo "=== กำลังเริ่มประมวลผล import_stock_movement ===\n";
echo "กำลังส่งคำสั่งคิวรีไปยัง SQL Server...\n";
if (ob_get_level() > 0) ob_flush();
flush();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_product_stock_balance WHERE SKU_CODE = :SKU_CODE AND ICCAT_CODE = :ICCAT_CODE AND DI_DATE = :DI_DATE AND WL_CODE = :WL_CODE AND WH_CODE = :WH_CODE");
$stmt_update = $conn->prepare("UPDATE ims_product_stock_balance SET ICCAT_NAME=:ICCAT_NAME,SKU_NAME=:SKU_NAME,UTQ_QTY=:UTQ_QTY,QTY=:QTY,WL_NAME=:WL_NAME,WH_NAME=:WH_NAME,update_date=:update_date WHERE SKU_CODE = :SKU_CODE AND DI_DATE = :DI_DATE AND ICCAT_CODE=:ICCAT_CODE AND WL_CODE=:WL_CODE AND WH_CODE=:WH_CODE");
$stmt_insert = $conn->prepare("INSERT INTO ims_product_stock_balance(ICCAT_CODE,ICCAT_NAME,DI_DATE,SKU_CODE,SKU_NAME,WH_CODE,WL_CODE,UTQ_QTY,QTY,WH_NAME,WL_NAME,create_date) VALUES (:ICCAT_CODE,:ICCAT_NAME,:DI_DATE,:SKU_CODE,:SKU_NAME,:WH_CODE,:WL_CODE,:UTQ_QTY,:QTY,:WH_NAME,:WL_NAME,:create_date)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;
$batch_count = 0;
$batch_size = 2000;
$start_time = microtime(true);

try {
    $statement = $conn_sqlsvr->query($sql_main);
    echo "คิวรี SQL Server สำเร็จ กำลังดึงและบันทึกข้อมูลเข้า MySQL...\n";
    if (ob_get_level() > 0) ob_flush();
    flush();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([
            ':SKU_CODE'   => $result["SKU_CODE"],
            ':ICCAT_CODE' => $result["ICCAT_CODE"],
            ':DI_DATE'    => $result["DI_DATE"],
            ':WL_CODE'    => $result["WL_CODE"],
            ':WH_CODE'    => $result["WH_CODE"]
        ]);

        if ($stmt_find->fetchColumn() > 0) {
            $stmt_update->execute([
                ':ICCAT_NAME'  => $result["ICCAT_NAME"],
                ':SKU_NAME'    => $result["SKU_NAME"],
                ':UTQ_QTY'     => $result["UTQ_QTY"],
                ':QTY'         => $result["QTY"],
                ':WL_NAME'     => $result["WL_NAME"],
                ':WH_NAME'     => $result["WH_NAME"],
                ':update_date' => $update_date,
                ':SKU_CODE'    => $result["SKU_CODE"],
                ':DI_DATE'     => $result["DI_DATE"],
                ':ICCAT_CODE'  => $result["ICCAT_CODE"],
                ':WL_CODE'     => $result["WL_CODE"],
                ':WH_CODE'     => $result["WH_CODE"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':ICCAT_CODE'  => $result["ICCAT_CODE"],
                ':ICCAT_NAME'  => $result["ICCAT_NAME"],
                ':DI_DATE'     => $result["DI_DATE"],
                ':SKU_CODE'    => $result["SKU_CODE"],
                ':SKU_NAME'    => $result["SKU_NAME"],
                ':WH_CODE'     => $result["WH_CODE"],
                ':WL_CODE'     => $result["WL_CODE"],
                ':UTQ_QTY'     => $result["UTQ_QTY"],
                ':QTY'         => $result["QTY"],
                ':WH_NAME'     => $result["WH_NAME"],
                ':WL_NAME'     => $result["WL_NAME"],
                ':create_date' => $create_date
            ]);
            $count_insert++;
        }

        $batch_count++;
        if ($batch_count % $batch_size === 0) {
            $conn->commit();
            $conn->beginTransaction();

            $elapsed = round(microtime(true) - $start_time, 1);
            echo "ความคืบหน้า: ประมวลผลแล้ว " . number_format($batch_count) . " รายการ (Insert: " . number_format($count_insert) . ", Update: " . number_format($count_update) . ") [{$elapsed}s]\n";
            if (ob_get_level() > 0) ob_flush();
            flush();
        }
    }
    $conn->commit();

    $total_elapsed = round(microtime(true) - $start_time, 2);
    echo "==================================================\n";
    echo "stock_movement import finished. Total: " . number_format($batch_count) . " รายการ (Insert: " . number_format($count_insert) . ", Update: " . number_format($count_update) . ") เวลาที่ใช้: {$total_elapsed} วินาที\n";
    echo "==================================================\n";
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error in stock_movement import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;










