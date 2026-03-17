<?php
include dirname(__DIR__) . '/config/connect_db.php';

try {
    // เพิ่ม UNIQUE KEY บน product_id
    $conn->exec("ALTER TABLE ims_product ADD UNIQUE KEY idx_product_id (product_id)");
    echo "เพิ่ม UNIQUE KEY idx_product_id สำเร็จ\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

try {
    // เพิ่ม UNIQUE KEY บน product_key  
    $conn->exec("ALTER TABLE ims_product ADD UNIQUE KEY idx_product_key (product_key)");
    echo "เพิ่ม UNIQUE KEY idx_product_key สำเร็จ\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "เสร็จสิ้น\n";
