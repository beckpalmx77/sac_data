<?php
date_default_timezone_set("Asia/Bangkok");

// 1. Include การเชื่อมต่อฐานข้อมูล
$start_time = microtime(true);
$start_datetime = date("Y-m-d H:i:s");
echo "=== เริ่มต้นงาน: $start_datetime ===\n\n";

include '../config/connect_db2.php';        // ตัวแปร $conn (MySQL - ตรวจพบว่าเป็น PDO)
include '../config/connect_sqlserver.php'; // ตัวแปร $conn_sqlsvr (MSSQL - เป็น PDO)

// 2. คำสั่ง SQL สำหรับดึงข้อมูลจาก MSSQL
$ms_sql = "
SELECT 
    V.SKU_CODE, 
    V.SKU_NAME, 
    SUM(CAST(V.QTY AS DECIMAL(10,2))) as TOTAL_QTY
FROM v_stock_movement V WITH (NOLOCK)
LEFT JOIN SKUMASTER WITH (NOLOCK) ON V.SKU_CODE = SKUMASTER.SKU_CODE
LEFT JOIN ICCAT WITH (NOLOCK) ON SKUMASTER.SKU_ICCAT = ICCAT.ICCAT_KEY
WHERE SKUMASTER.SKU_ENABLE = 'Y'    
AND (
    ICCAT.ICCAT_CODE LIKE '1SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '2SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '3SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '4SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '5SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '6SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '7SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '8SAC%' OR 
    ICCAT.ICCAT_CODE LIKE '9SAC%'
)
GROUP BY V.SKU_CODE, V.SKU_NAME
";

try {
    // ดึงข้อมูลจาก MSSQL (PDO)
    echo "1. กำลังเชื่อมต่อ MSSQL และดึงข้อมูล...\n";
    $stmt_ms = $conn_sqlsvr->query($ms_sql);

    // นับจำนวนข้อมูล
    $all_rows = $stmt_ms->fetchAll(PDO::FETCH_ASSOC);
    $total_rows = count($all_rows);
    echo "   พบข้อมูลทั้งหมด: $total_rows รายการ\n";

    if ($total_rows == 0) {
        echo "ไม่พบข้อมูลในการอัพเดท\n";
        exit;
    }

    // 3. เตรียมคำสั่ง UPDATE สำหรับ MySQL (PDO)
    // ใช้เครื่องหมาย : แทน ? เพื่อความชัดเจนใน PDO
    $my_sql = "
        UPDATE ims_product 
        SET quantity = :qty
        WHERE product_id = :sku
    ";

    $stmt_my = $conn->prepare($my_sql);
    $count_updated = 0;

    // 4. Loop ข้อมูล
    echo "2. กำลังอัพเดทข้อมูล...\n";
    $current = 0;
    foreach ($all_rows as $row) {
        $current++;
        $sku_code  = $row['SKU_CODE'];
        $total_qty = $row['TOTAL_QTY'];

        // แสดงความคืบหน้าทุก 100 รายการ
        if ($current % 100 == 0 || $current == $total_rows) {
            echo "   กำลังประมวลผล: [$current/$total_rows] อัพเดทแล้ว: $count_updated รายการ\r";
        }

        // การส่งค่าแบบ PDO execute (แทนที่ bind_param เดิม)
        $params = [
            ':qty'  => $total_qty,
            ':sku'  => $sku_code
        ];

        if ($stmt_my->execute($params)) {
            // ใน PDO ใช้ rowCount() แทน affected_rows
            if ($stmt_my->rowCount() > 0) {
                $count_updated++;
            }
        } else {
            // กรณี execute ไม่ผ่าน
            $errorInfo = $stmt_my->errorInfo();
            echo "Error updating SKU: " . $sku_code . " - " . $errorInfo[2] . "\n";
        }
    }

    echo "\n3. อัพเดทเสร็จสิ้น\n";
    echo "ระบบทำการ Update ข้อมูลสำเร็จทั้งหมด: $count_updated รายการ\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// 5. คำนวณเวลาที่ใช้
$end_time = microtime(true);
$end_datetime = date("Y-m-d H:i:s");
$duration = $end_time - $start_time;

echo "\n=== สรุปผลการทำงาน ===\n";
echo "เริ่มต้น: $start_datetime\n";
echo "สิ้นสุด: $end_datetime\n";
echo "ใช้เวลาทั้งหมด: " . number_format($duration, 2) . " วินาที\n";
echo "=======================\n";

// ปิดการเชื่อมต่อ (PDO ใช้การกำหนดเป็น null)
$conn_sqlsvr = null;
$conn = null;
