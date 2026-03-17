<?php
include dirname(__DIR__) . '/config/connect_db.php';

try {
    // ลบ duplicate โดยเก็บ id สูงสุด (ล่าสุด)
    $conn->exec("DELETE FROM ims_product WHERE id NOT IN (
        SELECT * FROM (
            SELECT MAX(id) FROM ims_product GROUP BY product_id
        ) AS keep
    )");
    echo "ลบ duplicate product_id สำเร็จ\n";
} catch (PDOException $e) {
    echo "Error product_id: " . $e->getMessage() . "\n";
}

try {
    $conn->exec("DELETE FROM ims_product WHERE id NOT IN (
        SELECT * FROM (
            SELECT MAX(id) FROM ims_product GROUP BY product_key
        ) AS keep
    )");
    echo "ลบ duplicate product_key สำเร็จ\n";
} catch (PDOException $e) {
    echo "Error product_key: " . $e->getMessage() . "\n";
}

try {
    $conn->exec("ALTER TABLE ims_product ADD UNIQUE KEY idx_product_id (product_id)");
    echo "เพิ่ม UNIQUE KEY idx_product_id สำเร็จ\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

try {
    $conn->exec("ALTER TABLE ims_product ADD UNIQUE KEY idx_product_key (product_key)");
    echo "เพิ่ม UNIQUE KEY idx_product_key สำเร็จ\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "เสร็จสิ้น\n";
