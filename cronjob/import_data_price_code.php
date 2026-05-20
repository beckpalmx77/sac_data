<?php
include('../config/connect_db2.php');
include('../config/connect_sqlserver.php');

// ดึงข้อมูลจาก SQL Server
$sql_sqlsvr = "SELECT ARPRB_CODE, ARPRB_NAME, ARPRB_ENABLE FROM ARPRICETAB ORDER BY ARPRB_CODE";
$statement = $conn_sqlsvr->query($sql_sqlsvr);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

$count_success = 0;

// เตรียมคำสั่ง SQL (Prepare) เพียงครั้งเดียวนอก Loop
// ใช้ ON DUPLICATE KEY UPDATE เพื่อ Insert หรือ Update ในคำสั่งเดียว (Upsert)
$sql_upsert = "INSERT INTO ims_price_code (price_code, price_detail, status) 
               VALUES (:price_code, :price_detail, :status)
               ON DUPLICATE KEY UPDATE 
               price_detail = VALUES(price_detail), 
               status = VALUES(status)";

$stmt_upsert = $conn->prepare($sql_upsert);

// เปิด Transaction เพื่อให้ทำงานรวดเดียว
$conn->beginTransaction();

try {
    foreach ($results as $result) {
        $price_code = trim($result["ARPRB_CODE"]);
        $price_detail = trim($result["ARPRB_NAME"]);
        $status = trim($result["ARPRB_ENABLE"]);

        // แก้ไขบั๊กจากของเดิม: เปลี่ยน $brand_id เป็น $price_code
        if (empty($price_code)) continue;

        // รันคำสั่ง SQL
        $stmt_upsert->execute([
            ':price_code' => $price_code,
            ':price_detail' => $price_detail,
            ':status' => $status
        ]);

        $count_success++;
        echo "$price_code | $price_detail | [OK]\n";
    }

    // บันทึกข้อมูลทั้งหมดลงฐานข้อมูล
    $conn->commit();

} catch (Exception $e) {
    // ยกเลิกการทำงานทั้งหมดหากมี Error
    $conn->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== เสร็จสิ้นการทำงาน ===\n";
echo "Processed: $count_success records\n";