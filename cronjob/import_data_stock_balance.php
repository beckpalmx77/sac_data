<?php

ini_set('display_errors', 1);
error_reporting(~0);
set_time_limit(0);
ini_set('memory_limit', '1024M');

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");

include("../cond_file/query-product-stock-balance.php");

$start_time = microtime(true);

echo "=== กำลังเริ่มประมวลผล import_data_stock_balance ===\n";
echo "กำลังส่งคำสั่งคิวรีไปยัง SQL Server...\n";
if (ob_get_level() > 0) ob_flush();
flush();

// Ensure UNIQUE KEY index exists on composite columns for high-speed UPSERT
try {
    $idx_check = $conn->query("SHOW INDEX FROM ims_product_stock_balance WHERE Key_name = 'idx_unique_stock_bal'");
    if (!$idx_check->fetch()) {
        $conn->exec("ALTER TABLE ims_product_stock_balance ADD UNIQUE KEY idx_unique_stock_bal (ICCAT_CODE(50), SKU_CODE(50), WH_CODE(50), WL_CODE(50), SKM_LOT_NO(50), SKM_SERIAL(50))");
        echo "สร้าง Unique Key idx_unique_stock_bal สำเร็จ\n";
    }
} catch (Exception $e) {
    // Unique key creation skipped if already existing
}

try {
    $sql_sqlsvr = $select_query;
    $stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
    $stmt_sqlsvr->execute();

    echo "คิวรี SQL Server สำเร็จ กำลังสตรีมและบันทึกข้อมูลเข้า MySQL (Batch UPSERT)...\n";
    if (ob_get_level() > 0) ob_flush();
    flush();

    $batch_size = 1000;
    $current = 0;
    $values_batch = [];

    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $current++;

        $iccat_code = $result_sqlsvr["ICCAT_CODE"] ?? '';
        $iccat_name = $result_sqlsvr["ICCAT_NAME"] ?? '';
        $di_date    = $result_sqlsvr["DI_DATE"] ?? '';
        $sku_code   = $result_sqlsvr["SKU_CODE"] ?? '';
        $sku_name   = $result_sqlsvr["SKU_NAME"] ?? '';
        $wh_code    = $result_sqlsvr["WH_CODE"] ?? '';
        $wl_code    = $result_sqlsvr["WL_CODE"] ?? '';
        $skm_lot_no = $result_sqlsvr["SKM_LOT_NO"] ?? '';
        $skm_serial = $result_sqlsvr["SKM_SERIAL"] ?? '';
        $utq_name   = $result_sqlsvr["UTQ_NAME"] ?? '';
        $utq_qty    = isset($result_sqlsvr["UTQ_QTY"]) ? (float)$result_sqlsvr["UTQ_QTY"] : 0;
        $qty        = isset($result_sqlsvr["QTY"]) ? (float)$result_sqlsvr["QTY"] : 0;
        $stock_cost = isset($result_sqlsvr["STOCK_COST"]) ? (float)$result_sqlsvr["STOCK_COST"] : 0;
        $ac_cost    = isset($result_sqlsvr["AC_COST"]) ? (float)$result_sqlsvr["AC_COST"] : 0;
        $std_cost   = isset($result_sqlsvr["STD_COST"]) ? (float)$result_sqlsvr["STD_COST"] : 0;

        $q_iccat_code = $conn->quote($iccat_code);
        $q_iccat_name = $conn->quote($iccat_name);
        $q_di_date    = $conn->quote($di_date);
        $q_sku_code   = $conn->quote($sku_code);
        $q_sku_name   = $conn->quote($sku_name);
        $q_wh_code    = $conn->quote($wh_code);
        $q_wl_code    = $conn->quote($wl_code);
        $q_skm_lot_no = $conn->quote($skm_lot_no);
        $q_skm_serial = $conn->quote($skm_serial);
        $q_utq_name   = $conn->quote($utq_name);

        $values_batch[] = "($q_iccat_code,$q_iccat_name,$q_di_date,$q_sku_code,$q_sku_name,$q_wh_code,$q_wl_code,$q_skm_lot_no,$q_skm_serial,$q_utq_name,$utq_qty,$qty,$stock_cost,$ac_cost,$std_cost)";

        if (count($values_batch) >= $batch_size) {
            $values_str = implode(",", $values_batch);

            $sql_upsert = "INSERT INTO ims_product_stock_balance
                (ICCAT_CODE, ICCAT_NAME, DI_DATE, SKU_CODE, SKU_NAME, WH_CODE, WL_CODE, SKM_LOT_NO, SKM_SERIAL, UTQ_NAME, UTQ_QTY, QTY, STOCK_COST, AC_COST, STD_COST)
                VALUES $values_str
                ON DUPLICATE KEY UPDATE
                    ICCAT_NAME = VALUES(ICCAT_NAME),
                    DI_DATE = VALUES(DI_DATE),
                    SKU_NAME = VALUES(SKU_NAME),
                    UTQ_NAME = VALUES(UTQ_NAME),
                    UTQ_QTY = VALUES(UTQ_QTY),
                    QTY = VALUES(QTY),
                    STOCK_COST = VALUES(STOCK_COST),
                    AC_COST = VALUES(AC_COST),
                    STD_COST = VALUES(STD_COST)";

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
            (ICCAT_CODE, ICCAT_NAME, DI_DATE, SKU_CODE, SKU_NAME, WH_CODE, WL_CODE, SKM_LOT_NO, SKM_SERIAL, UTQ_NAME, UTQ_QTY, QTY, STOCK_COST, AC_COST, STD_COST)
            VALUES $values_str
            ON DUPLICATE KEY UPDATE
                ICCAT_NAME = VALUES(ICCAT_NAME),
                DI_DATE = VALUES(DI_DATE),
                SKU_NAME = VALUES(SKU_NAME),
                UTQ_NAME = VALUES(UTQ_NAME),
                UTQ_QTY = VALUES(UTQ_QTY),
                QTY = VALUES(QTY),
                STOCK_COST = VALUES(STOCK_COST),
                AC_COST = VALUES(AC_COST),
                STD_COST = VALUES(STD_COST)";

        try {
            $conn->exec($sql_upsert);
        } catch (PDOException $e) {
            echo "Error during final batch upsert: " . $e->getMessage() . "\n";
        }
        $values_batch = [];
    }

    $total_elapsed = round(microtime(true) - $start_time, 2);
    echo "==================================================\n";
    echo "stock_balance import finished. Total: " . number_format($current) . " รายการ\n";
    echo "เวลาที่ใช้ทั้งหมด: {$total_elapsed} วินาที\n";
    echo "==================================================\n";
} catch (Exception $e) {
    echo "Error in stock_balance import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;
$conn = null;




