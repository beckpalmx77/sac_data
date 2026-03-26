<?php
include dirname(__DIR__) . "/config/connect_db2s.php";

$cnt = $conn2->query("SELECT COUNT(*) FROM ims_product")->fetchColumn();
file_put_contents("test_count.txt", "Count: " . $cnt . "\n");

$rows = $conn2->query("SELECT id, product_id, name_t, price FROM ims_product ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
file_put_contents("test_rows.txt", print_r($rows, true));

echo "Done. Count: $cnt\n";
