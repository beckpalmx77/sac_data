<?php
include('../config/connect_db.php');
include('../config/connect_sqlserver.php');
include('../cond_file/query_product_stock.php');

//if ($_POST["action"] === 'GET_DATA') {

    //$product_id = $_POST["product_id"];

    //$product_id = 'DS2157016-HT603';

    //$sql_main = $select_query_stock . $sql_cond_stock . " AND SKU_CODE = '" . $product_id . "' " . $sql_group_stock . $sql_order_stock;
    $sql_main = $select_query_stock . $sql_cond_stock . $sql_group_stock . $sql_order_stock;

    //$my_file = fopen("sql_getsql.txt", "w") or die("Unable to open file!");
    //fwrite($my_file,$sql_main);
    //fclose($my_file);
    $create_date = date("Y-m-d H:i:s");
    $update_date = date("Y-m-d H:i:s");

    $statement = $conn_sqlsvr->query($sql_main);
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    $stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_product_stock_balance WHERE SKU_CODE = :SKU_CODE AND ICCAT_CODE = :ICCAT_CODE AND DI_DATE = :DI_DATE AND WL_CODE = :WL_CODE AND WH_CODE = :WH_CODE");
    $stmt_update = $conn->prepare("UPDATE ims_product_stock_balance SET ICCAT_NAME=:ICCAT_NAME,SKU_NAME=:SKU_NAME,UTQ_QTY=:UTQ_QTY,QTY=:QTY,WL_NAME=:WL_NAME,WH_NAME=:WH_NAME,update_date=:update_date WHERE SKU_CODE = :SKU_CODE AND DI_DATE = :DI_DATE AND ICCAT_CODE=:ICCAT_CODE AND WL_CODE=:WL_CODE AND WH_CODE=:WH_CODE");
    $stmt_insert = $conn->prepare("INSERT INTO ims_product_stock_balance(ICCAT_CODE,ICCAT_NAME,DI_DATE,SKU_CODE,SKU_NAME,WH_CODE,WL_CODE,UTQ_QTY,QTY,WH_NAME,WL_NAME,create_date) VALUES (:ICCAT_CODE,:ICCAT_NAME,:DI_DATE,:SKU_CODE,:SKU_NAME,:WH_CODE,:WL_CODE,:UTQ_QTY,:QTY,:WH_NAME,:WL_NAME,:create_date)");

    $conn->beginTransaction();
    $count_insert = 0;
    $count_update = 0;

    try {
        foreach ($results as $result) {
            $stmt_find->execute([
                ':SKU_CODE' => $result["SKU_CODE"],
                ':ICCAT_CODE' => $result["ICCAT_CODE"],
                ':DI_DATE' => $result["DI_DATE"],
                ':WL_CODE' => $result["WL_CODE"],
                ':WH_CODE' => $result["WH_CODE"]
            ]);

            if ($stmt_find->fetchColumn() > 0) {
                $stmt_update->execute([
                    ':ICCAT_NAME' => $result["ICCAT_NAME"],
                    ':SKU_NAME' => $result["SKU_NAME"],
                    ':UTQ_QTY' => $result["UTQ_QTY"],
                    ':QTY' => $result["QTY"],
                    ':WL_NAME' => $result["WL_NAME"],
                    ':WH_NAME' => $result["WH_NAME"],
                    ':update_date' => $update_date,
                    ':SKU_CODE' => $result["SKU_CODE"],
                    ':DI_DATE' => $result["DI_DATE"],
                    ':ICCAT_CODE' => $result["ICCAT_CODE"],
                    ':WL_CODE' => $result["WL_CODE"],
                    ':WH_CODE' => $result["WH_CODE"]
                ]);
                $count_update++;
            } else {
                $stmt_insert->execute([
                    ':ICCAT_CODE' => $result["ICCAT_CODE"],
                    ':ICCAT_NAME' => $result["ICCAT_NAME"],
                    ':DI_DATE' => $result["DI_DATE"],
                    ':SKU_CODE' => $result["SKU_CODE"],
                    ':SKU_NAME' => $result["SKU_NAME"],
                    ':WH_CODE' => $result["WH_CODE"],
                    ':WL_CODE' => $result["WL_CODE"],
                    ':UTQ_QTY' => $result["UTQ_QTY"],
                    ':QTY' => $result["QTY"],
                    ':WH_NAME' => $result["WH_NAME"],
                    ':WL_NAME' => $result["WL_NAME"],
                    ':create_date' => $create_date
                ]);
                $count_insert++;
            }
        }
        $conn->commit();
        echo "stock_movement import finished. Insert: $count_insert, Update: $count_update\n";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error in stock_movement import: " . $e->getMessage() . "\n";
    }

    $conn_sqlsvr = null;




