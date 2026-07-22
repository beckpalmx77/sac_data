<?php

ini_set('display_errors', 1);
error_reporting(~0);

include ("../config/connect_sqlserver.php");
include ("../config/connect_db.php");

include ("../cond_file/doc_info_credit_sale.php");

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

$stmt_find = $conn->prepare("SELECT COUNT(*) FROM ims_price_approve_header WHERE DI_KEY = :DI_KEY");
$stmt_insert = $conn->prepare("INSERT INTO ims_price_approve_header(DI_KEY,doc_no,doc_date,customer_id,customer_name) VALUES (:DI_KEY,:doc_no,:doc_date,:customer_id,:customer_name)");

$conn->beginTransaction();
$count_insert = 0;
$count_dup = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $stmt_find->execute([':DI_KEY' => $result_sqlsvr["DI_KEY"]]);
        if ($stmt_find->fetchColumn() > 0) {
            $count_dup++;
        } else {
            $doc_date = substr($result_sqlsvr["DI_DATE"], 8, 2) . "/" . substr($result_sqlsvr["DI_DATE"], 5, 2) . "/" . strval(intval(substr($result_sqlsvr["DI_DATE"], 0, 4)) + 543);

            $stmt_insert->execute([
                ':DI_KEY' => $result_sqlsvr["DI_KEY"],
                ':doc_no' => $result_sqlsvr["DI_REF"],
                ':doc_date' => $doc_date,
                ':customer_id' => $result_sqlsvr["AR_CODE"],
                ':customer_name' => $result_sqlsvr["AR_NAME"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "credit_sale import finished. Insert: $count_insert, Duplicate: $count_dup\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in credit_sale import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;


