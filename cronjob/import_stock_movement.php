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
$start_time = microtime(true);

echo "=== กำลังเริ่มประมวลผล import_stock_movement ===\n";
echo "กำลังส่งคำสั่งคิวรีไปยัง SQL Server...\n";
if (ob_get_level() > 0) ob_flush();
flush();

// Ensure UNIQUE KEY index exists on composite columns for high-speed UPSERT
try {
    $idx_check = $conn->query("SHOW INDEX FROM ims_product_stock_balance WHERE Key_name = 'idx_unique_stock_mov'");
    if (!$idx_check->fetch()) {
        $conn->exec("ALTER TABLE ims_product_stock_balance ADD UNIQUE KEY idx_unique_stock_mov (SKU_CODE(50), ICCAT_CODE(50), DI_DATE(20), WL_CODE(50), WH_CODE(50))");
        echo "สร้าง Unique Key idx_unique_stock_mov สำเร็จ\n";
    }
} catch (Exception $e) {
    // Unique key creation skipped if already existing
}

try {
    $statement = $conn_sqlsvr->query($sql_main);
    echo "คิวรี SQL Server สำเร็จ กำลังสตรีมและบันทึกข้อมูลเข้า MySQL (Batch UPSERT)...\n";
    if (ob_get_level() > 0) ob_flush();
    flush();

    $batch_size = 1000;
    $current = 0;
    $values_batch = [];

    $q_create_date = $conn->quote($create_date);
    $q_update_date = $conn->quote($update_date);

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $current++;

        $sku_code   = $result["SKU_CODE"] ?? '';
        $iccat_code = $result["ICCAT_CODE"] ?? '';
        $iccat_name = $result["ICCAT_NAME"] ?? '';
        $di_date    = $result["DI_DATE"] ?? '';
        $sku_name   = $result["SKU_NAME"] ?? '';
        $wh_code    = $result["WH_CODE"] ?? '';
        $wh_name    = $result["WH_NAME"] ?? '';
        $wl_code    = $result["WL_CODE"] ?? '';
        $wl_name    = $result["WL_NAME"] ?? '';
        $utq_qty    = isset($result["UTQ_QTY"]) ? (float)$result["UTQ_QTY"] : 0;
        $qty        = isset($result["QTY"]) ? (float)$result["QTY"] : 0;

        $q_sku_code   = $conn->quote($sku_code);
        $q_iccat_code = $conn->quote($iccat_code);
        $q_iccat_name = $conn->quote($iccat_name);
        $q_di_date    = $conn->quote($di_date);
        $q_sku_name   = $conn->quote($sku_name);
        $q_wh_code    = $conn->quote($wh_code);
        $q_wh_name    = $conn->quote($wh_name);
        $q_wl_code    = $conn->quote($wl_code);
        $q_wl_name    = $conn->quote($wl_name);

        $values_batch[] = "($q_iccat_code,$q_iccat_name,$q_di_date,$q_sku_code,$q_sku_name,$q_wh_code,$q_wl_code,$utq_qty,$qty,$q_wh_name,$q_wl_name,$q_create_date,$q_update_date)";

        if (count($values_batch) >= $batch_size) {
            $values_str = implode(",", $values_batch);

            $sql_upsert = "INSERT INTO ims_product_stock_balance
                (ICCAT_CODE, ICCAT_NAME, DI_DATE, SKU_CODE, SKU_NAME, WH_CODE, WL_CODE, UTQ_QTY, QTY, WH_NAME, WL_NAME, create_date, update_date)
                VALUES $values_str
                ON DUPLICATE KEY UPDATE
                    ICCAT_NAME = VALUES(ICCAT_NAME),
                    SKU_NAME = VALUES(SKU_NAME),
                    UTQ_QTY = VALUES(UTQ_QTY),
                    QTY = VALUES(QTY),
                    WH_NAME = VALUES(WH_NAME),
                    WL_NAME = VALUES(WL_NAME),
                    update_date = VALUES(update_date)";

            try {
                $conn->exec($sql_upsert);
            } catch (PDOException $e) {
                echo "Error during batch upsert: " . $e->getMessage() . "\n";
            }

            $elapsed = round(microtime(true) - $start_time, 1);
            echo "ความคืบหน้า: ประมวลผลแล้ว " . number_format($current) . " รายการ [{$elapsed}s]\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            $values_batch = [];
        }
    }

    // Process remaining batch
    if (!empty($values_batch)) {
        $values_str = implode(",", $values_batch);
        $sql_upsert = "INSERT INTO ims_product_stock_balance
            (ICCAT_CODE, ICCAT_NAME, DI_DATE, SKU_CODE, SKU_NAME, WH_CODE, WL_CODE, UTQ_QTY, QTY, WH_NAME, WL_NAME, create_date, update_date)
            VALUES $values_str
            ON DUPLICATE KEY UPDATE
                ICCAT_NAME = VALUES(ICCAT_NAME),
                SKU_NAME = VALUES(SKU_NAME),
                UTQ_QTY = VALUES(UTQ_QTY),
                QTY = VALUES(QTY),
                WH_NAME = VALUES(WH_NAME),
                WL_NAME = VALUES(WL_NAME),
                update_date = VALUES(update_date)";

        try {
            $conn->exec($sql_upsert);
        } catch (PDOException $e) {
            echo "Error during final batch upsert: " . $e->getMessage() . "\n";
        }
        $values_batch = [];
    }

    $total_elapsed = round(microtime(true) - $start_time, 2);
    echo "==================================================\n";
    echo "stock_movement import finished. Total: " . number_format($current) . " รายการ เวลาที่ใช้: {$total_elapsed} วินาที\n";
    echo "==================================================\n";
} catch (Exception $e) {
    echo "Error in stock_movement import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;
$conn = null;












