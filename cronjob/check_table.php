<?php
include dirname(__DIR__) . "/config/connect_db.php";
include dirname(__DIR__) . "/config/connect_db2s.php";

echo "=== Table Structure: ims_product (sac_data) ===\n";
$sql = "DESCRIBE ims_product";
$stmt = $conn->query($sql);
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . " - " . $col['Key'] . " - " . $col['Default'] . "\n";
}

echo "\n=== Table Structure: ims_product (sac_data2) ===\n";
$sql2 = "DESCRIBE ims_product";
$stmt2 = $conn2->query($sql2);
$columns2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns2 as $col) {
    echo $col['Field'] . " - " . $col['Type'] . " - " . $col['Key'] . " - " . $col['Default'] . "\n";
}

echo "\n=== Indexes: sac_data ===\n";
$sql3 = "SHOW INDEX FROM ims_product";
$stmt3 = $conn->query($sql3);
$indexes = $stmt3->fetchAll(PDO::FETCH_ASSOC);
foreach ($indexes as $idx) {
    echo $idx['Key_name'] . " - " . $idx['Column_name'] . " - " . $idx['Non_unique'] . "\n";
}

echo "\n=== Indexes: sac_data2 ===\n";
$sql4 = "SHOW INDEX FROM ims_product";
$stmt4 = $conn2->query($sql4);
$indexes2 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
foreach ($indexes2 as $idx) {
    echo $idx['Key_name'] . " - " . $idx['Column_name'] . " - " . $idx['Non_unique'] . "\n";
}

echo "\n=== Sample Data: sac_data ===\n";
$sql5 = "SELECT * FROM ims_product LIMIT 3";
$stmt5 = $conn->query($sql5);
$sample = $stmt5->fetchAll(PDO::FETCH_ASSOC);
print_r($sample);

echo "\n=== Sample Data: sac_data2 ===\n";
$sql6 = "SELECT * FROM ims_product LIMIT 3";
$stmt6 = $conn2->query($sql6);
$sample2 = $stmt6->fetchAll(PDO::FETCH_ASSOC);
print_r($sample2);
