<?php
// ตั้งค่าให้ Script ทำงานได้ไม่จำกัดเวลา (สำหรับข้อมูล 1.7 ล้านแถว)
set_time_limit(0);
ini_set('memory_limit', '512M');

require_once('../config/connect_db.php');

if (!$conn) {
    die("[ERROR] Connection failed: " . mysqli_connect_error() . PHP_EOL);
}

// ฟังก์ชันสำหรับแสดงข้อความออกหน้าจอ DOS
function cli_log($message) {
    echo "[" . date('H:i:s') . "] " . $message . PHP_EOL;
}

try {
    cli_log("--------------------------------------------------");
    cli_log("เริ่มกระบวนการจัดการข้อมูลซ้ำในตาราง ims_product");
    cli_log("--------------------------------------------------");

    // 1. นับจำนวนก่อนเริ่ม
    $sql_count = "SELECT COUNT(*) as total FROM ims_product";
    if ($conn instanceof mysqli) {
        $res = $conn->query($sql_count);
        $total_before = $res->fetch_assoc()['total'];
    } else {
        $stmt = $conn->query($sql_count);
        $total_before = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    cli_log("อ่านข้อมูลพบทั้งหมด: " . number_format($total_before) . " รายการ");
    cli_log("กำลังคัดกรองข้อมูลซ้ำ (อาจใช้เวลาสักครู่)...");

    // 2. ใช้ Temporary Table เพื่อความเร็วสูงสุดในกรณีข้อมูลหลักล้าน
    $sql_temp = "CREATE TEMPORARY TABLE temp_ims_product AS 
                 SELECT DISTINCT product_id, pgroup_id, brand_id, name_t, price_code 
                 FROM ims_product";

    $sql_del = "TRUNCATE TABLE ims_product";

    $sql_restore = "INSERT INTO ims_product (product_id, pgroup_id, brand_id, name_t, price_code) 
                    SELECT * FROM temp_ims_product";

    // รันคำสั่ง SQL
    if ($conn instanceof mysqli) {
        $conn->query($sql_temp);
        cli_log("สร้างตารางชั่วคราวสำเร็จ...");

        $conn->query($sql_del);
        cli_log("ล้างข้อมูลตารางเดิมเรียบร้อย...");

        $conn->query($sql_restore);
        cli_log("ย้ายข้อมูลที่คัดกรองแล้วกลับเข้าตารางหลัก...");

        $res_after = $conn->query($sql_count);
        $total_after = $res_after->fetch_assoc()['total'];
    } else {
        $conn->exec($sql_temp);
        $conn->exec($sql_del);
        $conn->exec($sql_restore);

        $stmt_after = $conn->query($sql_count);
        $total_after = $stmt_after->fetch(PDO::FETCH_ASSOC)['total'];
    }

    $deleted = $total_before - $total_after;

    // 3. สรุปผล
    cli_log("--------------------------------------------------");
    cli_log("เสร็จสิ้นการทำงาน!");
    cli_log("จำนวนข้อมูลเริ่มต้น: " . number_format($total_before));
    cli_log("จำนวนข้อมูลคงเหลือ: " . number_format($total_after));
    cli_log("จำนวนที่ลบออก (ซ้ำ): " . number_format($deleted));
    cli_log("--------------------------------------------------");

} catch (Exception $e) {
    cli_log("เกิดข้อผิดพลาด: " . $e->getMessage());
}