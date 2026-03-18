<?php

ini_set('display_errors', 1);
error_reporting(~0);

include(dirname(__DIR__) . "/config/connect_sqlserver.php");

echo "กำลังดึงข้อมูล BRAND จาก MSSQL...\n";

$sql_sqlsvr = "SELECT BRN_CODE, BRN_NAME FROM BRAND ORDER BY BRN_CODE";
$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$all_rows = $stmt_sqlsvr->fetchAll(PDO::FETCH_ASSOC);
$stmt_sqlsvr->closeCursor();
$total_rows = count($all_rows);

echo "พบข้อมูลทั้งหมด: $total_rows รายการ\n";

if ($total_rows == 0) {
    echo "ไม่พบข้อมูลในการนำเข้า\n";
    exit;
}

$output_file = dirname(__FILE__) . "/import_brand_sql.sql";
$sql = "-- Import BRAND to ims_brand\n-- Date: " . date("Y-m-d H:i:s") . "\n\n";

$sql .= "-- DB1 (sac_data)\n";
$sql .= "INSERT INTO ims_brand (brand_id, brand_name, status) VALUES\n";

$values = [];
foreach ($all_rows as $row) {
    $brand_id = trim($row["BRN_CODE"]);
    $brand_name = trim($row["BRN_NAME"]);
    if (empty($brand_id)) continue;
    $values[] = "('$brand_id', '" . addslashes($brand_name) . "', 'Active')";
}
$sql .= implode(",\n", $values) . "\n";
$sql .= "ON DUPLICATE KEY UPDATE brand_name = VALUES(brand_name), status = VALUES(status);\n\n";

$sql .= "-- DB2 (sac_data2)\n";
$sql .= "INSERT INTO ims_brand (brand_id, brand_name, status) VALUES\n";
$sql .= implode(",\n", $values) . "\n";
$sql .= "ON DUPLICATE KEY UPDATE brand_name = VALUES(brand_name), status = VALUES(status);\n";

file_put_contents($output_file, $sql);

echo "สร้างไฟล์ SQL: $output_file\n";
echo "จำนวน rows: " . count($values) . "\n";