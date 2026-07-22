<?php


ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");

include("../cond_file/query-product-price-main.php");

$qeury_where = "AND ICCAT_CODE IN ('2SAC01','2SAC02','2SAC03','2SAC02','2SAC04','2SAC05','2SAC06'
,'2SAC07','2SAC08','2SAC09','2SAC10','2SAC11','2SAC12','2SAC13','2SAC14','2SAC15') 
AND ARPRB_CODE = 'CP1'";


$sql_sqlsvr = $select_query . $sql_cond . $sql_order;

//$myfile = fopen("sqlqry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_tires_cockpit WHERE product_id = :product_id AND product_key = :product_key AND price_code = :price_code");
$stmt_update = $conn->prepare("UPDATE ims_tires_cockpit SET name_t=:name_t, brand_id=:brand_id, price=:price WHERE product_id = :product_id AND product_key = :product_key AND price_code = :price_code");
$stmt_insert = $conn->prepare("INSERT INTO ims_tires_cockpit(product_key,product_id,name_t,brand_id,price_code,price) VALUES (:product_key,:product_id,:name_t,:brand_id,:price_code,:price)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([
            ':product_id' => $result_sqlsvr["SKU_CODE"],
            ':product_key' => $result_sqlsvr["SKU_KEY"],
            ':price_code' => $result_sqlsvr["ARPRB_CODE"]
        ]);

        if ($stmt_find->fetchColumn() > 0) {
            $stmt_update->execute([
                ':name_t' => $result_sqlsvr["SKU_NAME"],
                ':brand_id' => $result_sqlsvr["BRN_CODE"],
                ':price' => $result_sqlsvr["ARPLU_U_PRC"],
                ':product_id' => $result_sqlsvr["SKU_CODE"],
                ':product_key' => $result_sqlsvr["SKU_KEY"],
                ':price_code' => $result_sqlsvr["ARPRB_CODE"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':product_key' => $result_sqlsvr["SKU_KEY"],
                ':product_id' => $result_sqlsvr["SKU_CODE"],
                ':name_t' => $result_sqlsvr["SKU_NAME"],
                ':brand_id' => $result_sqlsvr["BRN_CODE"],
                ':price_code' => $result_sqlsvr["ARPRB_CODE"],
                ':price' => $result_sqlsvr["ARPLU_U_PRC"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "product_tires_price_cp import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in product_tires_price_cp import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;



