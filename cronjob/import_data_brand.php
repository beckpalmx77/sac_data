<?php
include('../config/connect_db.php');
include('../config/connect_sqlserver.php');

$sql_sqlsvr = "SELECT BRN_CODE, BRN_NAME FROM BRAND ORDER BY BRN_CODE";
$statement = $conn_sqlsvr->query($sql_sqlsvr);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

$count_insert = 0;
$count_update = 0;

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_brand WHERE brand_id = :brand_id");
$stmt_update = $conn->prepare("UPDATE ims_brand SET brand_name = :brand_name, status = 'Active' WHERE brand_id = :brand_id");
$stmt_insert = $conn->prepare("INSERT INTO ims_brand (brand_id, brand_name, status) VALUES (:brand_id, :brand_name, 'Active')");

$conn->beginTransaction();

try {
    foreach ($results as $result) {
        $brand_id = trim($result["BRN_CODE"]);
        $brand_name = trim($result["BRN_NAME"]);

        if (empty($brand_id)) continue;

        $stmt_find->execute([':brand_id' => $brand_id]);
        $nRows = $stmt_find->fetchColumn();

        if ($nRows > 0) {
            $stmt_update->execute([':brand_name' => $brand_name, ':brand_id' => $brand_id]);
            if ($stmt_update->rowCount() > 0) {
                $count_update++;
            }
        } else {
            $stmt_insert->execute([':brand_id' => $brand_id, ':brand_name' => $brand_name]);
            $count_insert++;
        }
    }
    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== เสร็จสิ้นการทำงาน ===\n";
echo "Insert: $count_insert, Update: $count_update\n";