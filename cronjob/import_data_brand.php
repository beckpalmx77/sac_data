<?php
include('../config/connect_db.php');
include('../config/connect_sqlserver.php');

$sql_sqlsvr = "SELECT BRN_CODE, BRN_NAME FROM BRAND ORDER BY BRN_CODE";
$statement = $conn_sqlsvr->query($sql_sqlsvr);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

$count_insert = 0;
$count_update = 0;

foreach ($results as $result) {
    $brand_id = trim($result["BRN_CODE"]);
    $brand_name = trim($result["BRN_NAME"]);

    if (empty($brand_id)) continue;

    echo "$brand_id | $brand_name\n";

    $sql_find = "SELECT COUNT(*) FROM ims_brand WHERE brand_id = :brand_id";
    $stmt_find = $conn->prepare($sql_find);
    $stmt_find->bindParam(':brand_id', $brand_id, PDO::PARAM_STR);
    $stmt_find->execute();
    $nRows = $stmt_find->fetchColumn();

    if ($nRows > 0) {
        // UPDATE
        $sql = "UPDATE ims_brand SET brand_name = :brand_name, status = 'Active' WHERE brand_id = :brand_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':brand_name', $brand_name, PDO::PARAM_STR);
        $query->bindParam(':brand_id', $brand_id, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $count_update++;
            echo " [UPDATE OK]\n";
        }
    } else {
        // INSERT
        $sql = "INSERT INTO ims_brand (brand_id, brand_name, status) VALUES (:brand_id, :brand_name, 'Active')";
        $query = $conn->prepare($sql);
        $query->bindParam(':brand_id', $brand_id, PDO::PARAM_STR);
        $query->bindParam(':brand_name', $brand_name, PDO::PARAM_STR);
        $query->execute();
        $count_insert++;
        echo " [INSERT OK]\n";
    }
}

echo "\n=== เสร็จสิ้นการทำงาน ===\n";
echo "Insert: $count_insert, Update: $count_update\n";