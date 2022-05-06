<?php


header('Content-Type: application/json');

include("../config/connect_db.php");

//$month = $_POST["month"];
//$year = $_POST["year"];

$month = '4';
$year = '2022';


$sql_get = " SELECT BRANCH,DI_MONTH,DI_MONTH_NAME,DI_YEAR,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as  TRD_G_KEYIN
 FROM ims_product_sale_cockpit 
 WHERE DI_MONTH = '" . $month . "'
 AND DI_YEAR = '" . $year . "'
 AND PGROUP = 'P1'   
 GROUP BY  BRANCH,DI_MONTH,DI_MONTH_NAME,DI_YEAR 
 ORDER BY DI_MONTH , TRD_G_KEYIN DESC 
";

$return_arr = array();

$statement = $conn->query($sql_get);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {

    $sql_find = "SELECT * FROM ims_report_product_sale_summary WHERE BRANCH = '" . $result["BRANCH"] ."'"
        . " AND DI_MONTH = '" . $result["DI_MONTH"] . "'"
        . " AND DI_YEAR = '" . $result["DI_YEAR"] . "'";

    $nRows = $conn->query($sql_find)->fetchColumn();
    if ($nRows <= 0) {
        $sql = "INSERT INTO ims_product(product_key,product_id,pgroup_id,name_t,brand_id,price_code,price) 
                VALUES (:product_key,:product_id,:pgroup_id,:name_t,:brand_id,:price_code,:price)";
        $query = $conn->prepare($sql);
        $query->bindParam(':product_key', $result["SKU_KEY"], PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $conn->lastInsertId();
    }






}



