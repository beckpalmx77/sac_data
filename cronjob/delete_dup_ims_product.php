<?php
// 1. ดึงไฟล์เชื่อมต่อฐานข้อมูลมาใช้งาน
require_once('../config/connect_db.php');

/**
 * ตรวจสอบก่อนว่าตัวแปร $conn พร้อมใช้งานหรือไม่
 * (รองรับทั้งแบบ mysqli และ PDO)
 */
if (!$conn) {
    die("Connection failed: ไม่พบตัวแปรการเชื่อมต่อฐานข้อมูล (\$conn)");
}

try {
    echo "<h2>ระบบจัดการข้อมูลซ้ำในตาราง ims_product</h2>";

    // 2. คำสั่ง SQL สำหรับลบข้อมูลที่ซ้ำกัน
    // โดยจะเก็บรายการที่มี product_id น้อยที่สุดไว้เพียง 1 รายการ
    $sql = "DELETE p1 FROM ims_product p1
            INNER JOIN ims_product p2 
                ON p1.pgroup_id = p2.pgroup_id 
                AND p1.brand_id = p2.brand_id 
                AND p1.name_t = p2.name_t 
                AND p1.price_code = p2.price_code
            WHERE p1.product_id > p2.product_id";

    // 3. ตรวจสอบประเภทของการเชื่อมต่อและทำการ Query
    if ($conn instanceof mysqli) {
        // กรณีใช้ mysqli
        if ($conn->query($sql)) {
            $deletedRows = $conn->affected_rows;
            echo "สำเร็จ! ลบข้อมูลที่ซ้ำออกแล้วจำนวน: <b>$deletedRows</b> รายการ";
        } else {
            throw new Exception($conn->error);
        }
    } elseif ($conn instanceof PDO) {
        // กรณีใช้ PDO
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $deletedRows = $stmt->rowCount();
        echo "สำเร็จ! ลบข้อมูลที่ซ้ำออกแล้วจำนวน: <b>$deletedRows</b> รายการ";
    }

} catch (Exception $e) {
    echo "<span style='color:red;'>เกิดข้อผิดพลาด: </span>" . $e->getMessage();
}

// ปิดการเชื่อมต่อ (ถ้าจำเป็น)
// $conn->close();

