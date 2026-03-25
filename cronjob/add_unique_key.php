<?php
include dirname(__DIR__) . '/config/connect_db.php';
include dirname(__DIR__) . '/config/connect_db2s.php';

echo "=== เพิ่ม Unique Keys สำหรับ ims_product ===\n\n";

function addUniqueKey($conn, $dbName) {
    $indexes = $conn->query("SHOW INDEX FROM ims_product WHERE Key_name = 'unique_product_set'")->fetchAll();
    if (!empty($indexes)) {
        try {
            $conn->exec("ALTER TABLE ims_product DROP INDEX unique_product_set");
            echo "$dbName: ลบ unique key เดิมแล้ว\n";
        } catch (PDOException $e) {
            echo "$dbName: " . $e->getMessage() . "\n";
        }
    }
    
    $indexes2 = $conn->query("SHOW INDEX FROM ims_product WHERE Key_name = 'idx_unique_product'")->fetchAll();
    if (!empty($indexes2)) {
        echo "$dbName: idx_unique_product มีอยู่แล้ว\n";
        return;
    }

    try {
        $conn->exec("ALTER TABLE ims_product ADD UNIQUE KEY idx_unique_product (pgroup_id(50), brand_id, name_t(100), price_code(50), product_id)");
        echo "$dbName: เพิ่ม UNIQUE KEY idx_unique_product สำเร็จ\n";
    } catch (PDOException $e) {
        echo "$dbName Error: " . $e->getMessage() . "\n";
    }
}

addUniqueKey($conn, "DB1");
addUniqueKey($conn2, "DB2");

echo "\nเสร็จสิ้น\n";

$conn = null;
$conn2 = null;
