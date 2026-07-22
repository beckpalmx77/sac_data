<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");

include ("../cond_file/doc_info-query-001.php");

$doc_id_prefix = 'BKSV%';
$year = date("Y");
$month = date("m");

echo "Year = " . $year ; echo "\n\r"; echo "Month = " . $month ; echo "\n\r";

$sql_sqlsvr = $select_query . $sql_cond . " AND DI_REF like '" . $doc_id_prefix . "'"
    . " AND YEAR(DI_DATE) = " . $year
    . " AND MONTH(DI_DATE) = " . $month
    . $sql_order ;

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_price_approve_detail WHERE DI_KEY = :DI_KEY AND line_no = :line_no");
$stmt_insert = $conn->prepare("INSERT INTO ims_price_approve_detail(DI_KEY,doc_no,line_no,doc_date,customer_id,customer_name,product_id,product_name,price_normal,price_special,remark) VALUES (:DI_KEY,:doc_no,:line_no,:doc_date,:customer_id,:customer_name,:product_id,:product_name,:price_normal,:price_special,:remark)");

$conn->beginTransaction();
$count_insert = 0;
$count_dup = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([
            ':DI_KEY' => $result_sqlsvr["DI_KEY"],
            ':line_no' => $result_sqlsvr["TRD_SEQ"]
        ]);

        if ($stmt_find->fetchColumn() > 0) {
            $count_dup++;
        } else {
            $doc_date = substr($result_sqlsvr["DI_DATE"], 8, 2) . "/" . substr($result_sqlsvr["DI_DATE"], 5, 2) . "/" . strval(intval(substr($result_sqlsvr["DI_DATE"], 0, 4)) + 543);
            $remark = "Price/Unit ^^ TRD_K_U_PRC = " . $result_sqlsvr["TRD_K_U_PRC"] . " | TRD_U_PRC = " . $result_sqlsvr["TRD_U_PRC"];

            $stmt_insert->execute([
                ':DI_KEY' => $result_sqlsvr["DI_KEY"],
                ':doc_no' => $result_sqlsvr["DI_REF"],
                ':line_no' => $result_sqlsvr["TRD_SEQ"],
                ':doc_date' => $doc_date,
                ':customer_id' => $result_sqlsvr["AR_CODE"],
                ':customer_name' => $result_sqlsvr["AR_NAME"],
                ':product_id' => $result_sqlsvr["TRD_SH_CODE"],
                ':product_name' => $result_sqlsvr["TRD_SH_NAME"],
                ':price_normal' => $result_sqlsvr["TRD_K_U_PRC"],
                ':price_special' => $result_sqlsvr["TRD_K_U_PRC"],
                ':remark' => $remark
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "reserve_detail import finished. Insert: $count_insert, Duplicate: $count_dup\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in reserve_detail import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


