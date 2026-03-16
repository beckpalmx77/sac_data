<?php
// 1. Include การเชื่อมต่อฐานข้อมูล
include '../config/connect_db.php';        // ตัวแปร $conn (MySQL - DB1)
include '../config/connect_db2s.php';      // ตัวแปร $conn2 (MySQL - DB2)
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
    echo "กำลังอ่านข้อมูลจาก MSSQL...\n";
    $stmt_ms = $conn_sqlsvr->query($ms_sql);
    
    // นับจำนวนข้อมูลทั้งหมด
    $all_rows = $stmt_ms->fetchAll(PDO::FETCH_ASSOC);
    $total_rows = count($all_rows);
    echo "พบข้อมูลทั้งหมด: $total_rows รายการ\n";
    
    if ($total_rows == 0) {
        echo "ไม่พบข้อมูลในการอัพเดท\n";
        exit;
    }

    // 3. เตรียมคำสั่ง UPDATE สำหรับ MySQL (PDO)
    // ใช้เครื่องหมาย : แทน ? เพื่อความชัดเจนใน PDO
    $my_sql = "
        UPDATE ims_product 
        SET quantity = :qty, 
            name_t = :name,
            status = 'Active'
        WHERE product_id = :sku
    ";

    $stmt_my = $conn->prepare($my_sql);
    $stmt_my2 = $conn2->prepare($my_sql);
    $count_updated = 0;
    $count_updated2 = 0;
    $current = 0;
    $batch_size = 500;

    $conn->beginTransaction();
    $conn2->beginTransaction();

    // 4. Loop ข้อมูล
    echo "กำลังอัพเดทข้อมูล...\n";
    foreach ($all_rows as $row) {
        $current++;
        $sku_code  = $row['SKU_CODE'];
        $sku_name  = $row['SKU_NAME'];
        $total_qty = $row['TOTAL_QTY'];

        // แสดงความคืบหน้าทุก 100 รายการ
        if ($current % 100 == 0 || $current == $total_rows) {
            echo "\r[{$current}/{$total_rows}] DB1:{$count_updated} DB2:{$count_updated2} ";
        }

        // การส่งค่าแบบ PDO execute (แทนที่ bind_param เดิม)
        $params = [
            ':qty'  => $total_qty,
            ':name' => $sku_name,
            ':sku'  => $sku_code
        ];

        // Update DB1
        $stmt_my->execute($params);
        if ($stmt_my->rowCount() > 0) {
            $count_updated++;
        }

        // Update DB2
        $stmt_my2->execute($params);
        if ($stmt_my2->rowCount() > 0) {
            $count_updated2++;
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
    echo "อัพเดท DB1 (sac_data): $count_updated รายการ\n";
    echo "อัพเดท DB2 (sac_data2): $count_updated2 รายการ\n";
    echo "เสร็จสิ้นการทำงาน\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// 5. ปิดการเชื่อมต่อ (PDO ใช้การกำหนดเป็น null)
$conn_sqlsvr = null;
$conn = null;
$conn2 = null;
