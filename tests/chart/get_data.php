<?php
include("../../config/connect_db.php");

$str_return = "[";

$sql_get = " SELECT DI_YEAR,DI_MONTH,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as  TRD_G_KEYIN
 FROM ims_product_sale_cockpit 
 WHERE PGROUP like '%P1' AND DI_YEAR = '2021' 
 GROUP BY DI_MONTH,DI_YEAR 
 ORDER BY CAST(DI_MONTH AS UNSIGNED) ";

$statement = $conn->query($sql_get);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);


foreach ($results as $result) {
    if ($result['DI_MONTH'] == 12) {
        $str_return .= $result['TRD_G_KEYIN'];
    } else {
        $str_return .= $result['TRD_G_KEYIN'] . ",";
    }
}

$str_return .= "]";

echo $str_return;


?>