<?php
// 1. ตั้งค่า Environment สำหรับข้อมูลขนาดใหญ่ (1.7 ล้านแถว)
set_time_limit(0);
ini_set('memory_limit', '1024M');

require_once('../config/connect_db2s.php');

if (!$conn2) {
    die("[ERROR] " . date('H:i:s') . " - Connection failed: " . mysqli_connect_error() . PHP_EOL);
}

// ฟังก์ชันสำหรับแสดง Log ออกหน้าจอ DOS
function cli_log($message) {
    echo "[" . date('H:i:s') . "] " . $message . PHP_EOL;
}

try {
    cli_log("==================================================");
    cli_log("START: กระบวนการจัดการข้อมูลซ้ำในตาราง ims_product");
    cli_log("==================================================");

    // --- ส่วนที่ 1: ตรวจสอบและนับจำนวนก่อนเริ่ม ---
    $sql_total = "SELECT COUNT(*) as total FROM ims_product";

    // คำนวณหาจำนวน Row ที่ซ้ำ (นับเฉพาะส่วนเกินที่จะถูกลบ)
    $sql_duplicate = "SELECT SUM(duplicate_count - 1) as total_dup
                      FROM (
                          SELECT COUNT(*) as duplicate_count
                          FROM ims_product
                          GROUP BY product_id, pgroup_id, brand_id, name_t, price_code
                          HAVING COUNT(*) > 1
                      ) as dup_table";

    if ($conn2 instanceof mysqli) {
        $total_before = $conn2->query($sql_total)->fetch_assoc()['total'];
        $res_dup = $conn2->query($sql_duplicate)->fetch_assoc();
        $duplicate_to_delete = $res_dup['total_dup'] ?? 0;
    } else {
        $total_before = $conn2->query($sql_total)->fetch(PDO::FETCH_ASSOC)['total'];
        $res_dup = $conn2->query($sql_duplicate)->fetch(PDO::FETCH_ASSOC);
        $duplicate_to_delete = $res_dup['total_dup'] ?? 0;
    }

    cli_log("สถิติก่อนเริ่ม:");
    cli_log(" - ข้อมูลทั้งหมดในตาราง: " . number_format($total_before) . " รายการ");
    cli_log(" - ตรวจพบรายการที่ซ้ำ: " . number_format($duplicate_to_delete) . " รายการ");

    if ($duplicate_to_delete <= 0) {
        cli_log("ไม่พบข้อมูลซ้ำในระบบ จบการทำงาน.");
        cli_log("==================================================");
        exit;
    }

    // --- ส่วนที่ 2: เริ่มกระบวนการลบ (Execute) ---
    cli_log("กำลังดำเนินการลบข้อมูลที่ซ้ำ...");

    // 2.1 สร้างตารางชั่วคราวเก็บค่า Unique (รวมทุก field ที่มีในตาราง)
    $sql_temp = "CREATE TEMPORARY TABLE temp_ims_product AS 
                 SELECT DISTINCT product_key, product_id, pgroup_id, brand_id, name_t, price_code, price, unit_name 
                 FROM ims_product";

    // 2.2 ล้างตารางหลัก
    $sql_del = "TRUNCATE TABLE ims_product";

    // 2.3 ย้ายข้อมูลกลับ (รวมทุก field)
    $sql_restore = "INSERT INTO ims_product (product_key, product_id, pgroup_id, brand_id, name_t, price_code, price, unit_name) 
                    SELECT product_key, product_id, pgroup_id, brand_id, name_t, price_code, price, unit_name FROM temp_ims_product";

    if ($conn2 instanceof mysqli) {
        cli_log("Step 1: คัดกรองข้อมูลลงตารางชั่วคราว...");
        $conn2->query($sql_temp);

        cli_log("Step 2: ล้างข้อมูลตารางเดิม (Truncate)...");
        $conn2->query($sql_del);

        cli_log("Step 3: ย้ายข้อมูลกลับเข้าตารางหลัก...");
        $conn2->query($sql_restore);

        $total_after = $conn2->query($sql_total)->fetch_assoc()['total'];
    } else {
        cli_log("Step 1: คัดกรองข้อมูลลงตารางชั่วคราว...");
        $conn2->exec($sql_temp);

        cli_log("Step 2: ล้างข้อมูลตารางเดิม (Truncate)...");
        $conn2->exec($sql_del);

        cli_log("Step 3: ย้ายข้อมูลกลับเข้าตารางหลัก...");
        $conn2->exec($sql_restore);

        $total_after = $conn2->query($sql_total)->fetch(PDO::FETCH_ASSOC)['total'];
    }

    $actual_deleted = $total_before - $total_after;

    // --- ส่วนที่ 3: สรุปผลลัพธ์ ---
    cli_log("==================================================");
    cli_log("เสร็จสิ้นภารกิจ!");
    cli_log("จำนวนที่ลบออกไปจริง: " . number_format($actual_deleted) . " รายการ");
    cli_log("คงเหลือข้อมูลใช้งาน: " . number_format($total_after) . " รายการ");
    cli_log("==================================================");

} catch (Exception $e) {
    cli_log("[CRITICAL ERROR] " . $e->getMessage());
}