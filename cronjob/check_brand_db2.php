<?php

include(dirname(__DIR__) . "/config/connect_db2s.php");

// ลบ test record
$conn2->exec("DELETE FROM ims_brand WHERE brand_id = 'TEST'");

echo "=== sac_data2 - ims_brand ===\n";
$cnt = $conn2->query("SELECT COUNT(*) FROM ims_brand")->fetchColumn();
echo "COUNT: $cnt\n";

if ($cnt == 0) {
    echo "Table is empty - พร้อม test import\n";
} else {
    $stmt = $conn2->query("SELECT brand_id, brand_name, status FROM ims_brand");
    foreach ($stmt as $row) {
        echo $row['brand_id'] . " | " . $row['brand_name'] . "\n";
    }
}