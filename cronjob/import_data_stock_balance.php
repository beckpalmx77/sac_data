<?php

ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");

include("../cond_file/query-product-stock-balance.php");

$sql_sqlsvr = $select_query;

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);



$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

echo $sql_sqlsvr ;

$return_arr = array();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_product_stock_balance WHERE ICCAT_CODE = :ICCAT_CODE AND SKU_CODE = :SKU_CODE AND WH_CODE = :WH_CODE AND WL_CODE = :WL_CODE AND SKM_LOT_NO = :SKM_LOT_NO AND SKM_SERIAL = :SKM_SERIAL");
$stmt_update = $conn->prepare("UPDATE ims_product_stock_balance SET ICCAT_NAME=:ICCAT_NAME,DI_DATE=:DI_DATE,SKU_NAME=:SKU_NAME,UTQ_NAME=:UTQ_NAME,UTQ_QTY=:UTQ_QTY,QTY=:QTY,STOCK_COST=:STOCK_COST,AC_COST=:AC_COST,STD_COST=:STD_COST WHERE ICCAT_CODE = :ICCAT_CODE AND SKU_CODE = :SKU_CODE AND WH_CODE = :WH_CODE AND WL_CODE = :WL_CODE AND SKM_LOT_NO = :SKM_LOT_NO AND SKM_SERIAL = :SKM_SERIAL");
$stmt_insert = $conn->prepare("INSERT INTO ims_product_stock_balance(ICCAT_CODE,ICCAT_NAME,DI_DATE,SKU_CODE,SKU_NAME,WH_CODE,WL_CODE,SKM_LOT_NO,SKM_SERIAL,UTQ_NAME,UTQ_QTY,QTY,STOCK_COST,AC_COST,STD_COST) VALUES (:ICCAT_CODE,:ICCAT_NAME,:DI_DATE,:SKU_CODE,:SKU_NAME,:WH_CODE,:WL_CODE,:SKM_LOT_NO,:SKM_SERIAL,:UTQ_NAME,:UTQ_QTY,:QTY,:STOCK_COST,:AC_COST,:STD_COST)");

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([
            ':ICCAT_CODE' => $result_sqlsvr["ICCAT_CODE"],
            ':SKU_CODE'   => $result_sqlsvr["SKU_CODE"],
            ':WH_CODE'    => $result_sqlsvr["WH_CODE"],
            ':WL_CODE'    => $result_sqlsvr["WL_CODE"],
            ':SKM_LOT_NO' => $result_sqlsvr["SKM_LOT_NO"],
            ':SKM_SERIAL' => $result_sqlsvr["SKM_SERIAL"]
        ]);

        if ($stmt_find->fetchColumn() > 0) {
            $stmt_update->execute([
                ':ICCAT_NAME' => $result_sqlsvr["ICCAT_NAME"],
                ':DI_DATE'    => $result_sqlsvr["DI_DATE"],
                ':SKU_NAME'   => $result_sqlsvr["SKU_NAME"],
                ':UTQ_NAME'   => $result_sqlsvr["UTQ_NAME"],
                ':UTQ_QTY'    => $result_sqlsvr["UTQ_QTY"],
                ':QTY'        => $result_sqlsvr["QTY"],
                ':STOCK_COST' => $result_sqlsvr["STOCK_COST"],
                ':AC_COST'    => $result_sqlsvr["AC_COST"],
                ':STD_COST'   => $result_sqlsvr["STD_COST"],
                ':ICCAT_CODE' => $result_sqlsvr["ICCAT_CODE"],
                ':SKU_CODE'   => $result_sqlsvr["SKU_CODE"],
                ':WH_CODE'    => $result_sqlsvr["WH_CODE"],
                ':WL_CODE'    => $result_sqlsvr["WL_CODE"],
                ':SKM_LOT_NO' => $result_sqlsvr["SKM_LOT_NO"],
                ':SKM_SERIAL' => $result_sqlsvr["SKM_SERIAL"]
            ]);
            $count_update++;
        } else {
            $stmt_insert->execute([
                ':ICCAT_CODE' => $result_sqlsvr["ICCAT_CODE"],
                ':ICCAT_NAME' => $result_sqlsvr["ICCAT_NAME"],
                ':DI_DATE'    => $result_sqlsvr["DI_DATE"],
                ':SKU_CODE'   => $result_sqlsvr["SKU_CODE"],
                ':SKU_NAME'   => $result_sqlsvr["SKU_NAME"],
                ':WH_CODE'    => $result_sqlsvr["WH_CODE"],
                ':WL_CODE'    => $result_sqlsvr["WL_CODE"],
                ':SKM_LOT_NO' => $result_sqlsvr["SKM_LOT_NO"],
                ':SKM_SERIAL' => $result_sqlsvr["SKM_SERIAL"],
                ':UTQ_NAME'   => $result_sqlsvr["UTQ_NAME"],
                ':UTQ_QTY'    => $result_sqlsvr["UTQ_QTY"],
                ':QTY'        => $result_sqlsvr["QTY"],
                ':STOCK_COST' => $result_sqlsvr["STOCK_COST"],
                ':AC_COST'    => $result_sqlsvr["AC_COST"],
                ':STD_COST'   => $result_sqlsvr["STD_COST"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "stock_balance import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in stock_balance import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


