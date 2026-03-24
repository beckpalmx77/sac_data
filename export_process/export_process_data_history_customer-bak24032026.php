<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../config/connect_sqlserver.php');
include("../config/connect_db.php");
date_default_timezone_set('Asia/Bangkok');

$filename = "Data_Customer_History-" . date('m/d/Y H:i:s', time()) . ".csv";

@header('Content-type: text/csv; charset=UTF-8');
@header('Content-Encoding: UTF-8');
@header("Content-Disposition: attachment; filename=" . $filename);

$customer_name = $_POST["AR_NAME"] ?? "ชัยชาญ อ้นเพียรเอก";
$car_no = $_POST["car_no"] ?? "";

$doc_date_start_input = $_POST["doc_date_start"] ?? "01-03-2569";
$doc_date_to_input = $_POST["doc_date_to"] ?? date('d-m-Y');

$doc_date_start = substr($doc_date_start_input, 6, 4) . "/" . substr($doc_date_start_input, 3, 2) . "/" . substr($doc_date_start_input, 0, 2);
$doc_date_to = substr($doc_date_to_input, 6, 4) . "/" . substr($doc_date_to_input, 3, 2) . "/" . substr($doc_date_to_input, 0, 2);

$addb_phone = "";

$sql_cmd = "";

$data = "ลำดับที่,เลขที่เอกสาร,วันที่,ชื่อลูกค้า,หมายเลขโทรศัพท์,ทะเบียนรถ,ยี่ห้อรถ/รุ่น,เลขไมล์,รหัสสินค้า,ชื่อสินค้า,จำนวน,จำนวนเงิน(บาท)\n";

$sql_data_selectDetail =  "  SELECT TOP 5000
TRANSTKD.TRD_KEY , 
ADDRBOOK.ADDB_KEY , 
ADDRBOOK.ADDB_BRANCH , 
ADDRBOOK.ADDB_SEARCH ,
ADDRBOOK.ADDB_ADDB_1 , 
ADDRBOOK.ADDB_ADDB_2 , 
ADDRBOOK.ADDB_ADDB_3 ,
ADDRBOOK.ADDB_COMPANY ,
ADDRBOOK.ADDB_PHONE ,
ISNULL(PHONE.ADDB_PHONE, '') AS ADDB_PHONE_MAIN,
DOCINFO.DI_REF , 
DOCINFO.DI_DATE,
DAY(DI_DATE) AS DI_DAY ,
MONTH(DI_DATE) AS DI_MONTH ,
YEAR(DI_DATE) AS DI_YEAR ,
TRANSTKH.TRH_DI,
TRANSTKH.TRH_SHIP_ADDB,
SKUMASTER.SKU_CODE ,
SKUMASTER.SKU_NAME ,
TRANSTKD.TRD_QTY,
TRANSTKD.TRD_Q_FREE,
TRANSTKD.TRD_U_PRC,
TRANSTKD.TRD_B_SELL,
TRANSTKD.TRD_B_VAT,
TRANSTKD.TRD_B_AMT

FROM 
ADDRBOOK
INNER JOIN ARADDRESS ON ADDRBOOK.ADDB_KEY = ARADDRESS.ARA_ADDB
INNER JOIN ARDETAIL ON ARDETAIL.ARD_AR = ARADDRESS.ARA_AR
INNER JOIN DOCINFO ON DOCINFO.DI_KEY = ARDETAIL.ARD_DI
INNER JOIN TRANSTKH ON DOCINFO.DI_KEY = TRANSTKH.TRH_DI
INNER JOIN TRANSTKD ON TRANSTKH.TRH_KEY = TRANSTKD.TRD_TRH
INNER JOIN SKUMASTER ON TRANSTKD.TRD_SKU = SKUMASTER.SKU_KEY
LEFT JOIN (
    SELECT ARADDRESS.ARA_AR, ADDRBOOK.ADDB_PHONE
    FROM ARADDRESS
    INNER JOIN ADDRBOOK ON ADDRBOOK.ADDB_KEY = ARADDRESS.ARA_ADDB
    WHERE ARADDRESS.ARA_DEFAULT = 'Y'
) AS PHONE ON ARADDRESS.ARA_AR = PHONE.ARA_AR
 
WHERE
REPLACE(REPLACE(ADDRBOOK.ADDB_COMPANY, '  ', ' '), ' ', '%') like '%" . str_replace(" ", "%", $customer_name) . "%' AND
ADDRBOOK.ADDB_SEARCH like '%". $car_no . "%' AND
DOCINFO.DI_DATE BETWEEN '" . $doc_date_start . "' AND '" . $doc_date_to . "' ";

$order_by = " ORDER BY ADDRBOOK.ADDB_COMPANY , ADDRBOOK.ADDB_SEARCH , TRANSTKD.TRD_KEY , SKUMASTER.SKU_CODE ";

// ADDRBOOK.ADDB_KEY = '" . $result_sqlsvr_main["ADDB_KEY"] . "' AND


$sql_string = $sql_data_selectDetail . $order_by ;

/*
$myfile = fopen("query_export_history_customer.txt", "w") or die("Unable to open file!");
fwrite($myfile, "=== Main Query ===\n");
fwrite($myfile, $sql_string);
fwrite($myfile, "\n\n=== Parameters (From Form) ===\n");
fwrite($myfile, "customer_name: " . $customer_name . "\n");
fwrite($myfile, "car_no: " . $car_no . "\n");
fwrite($myfile, "doc_date_start: " . $doc_date_start . "\n");
fwrite($myfile, "doc_date_to: " . $doc_date_to . "\n");
fclose($myfile);
*/

$statement_sqlsvr = $conn_sqlsvr->query($sql_string);
$line = 0 ;
while ($result_sqlsvr_detail = $statement_sqlsvr->fetch(PDO::FETCH_ASSOC)) {

    $line++;

    $TRD_QTY = $result_sqlsvr_detail['TRD_Q_FREE'] > 0 ? $result_sqlsvr_detail['TRD_QTY'] = $result_sqlsvr_detail['TRD_QTY'] + $result_sqlsvr_detail['TRD_Q_FREE'] : $result_sqlsvr_detail['TRD_QTY'];

    $data .= $line . ",";
    $data .= $result_sqlsvr_detail['DI_REF'] . ",";
    $data .= $result_sqlsvr_detail['DI_DAY'] . "/" . $result_sqlsvr_detail['DI_MONTH'] . "/" . $result_sqlsvr_detail['DI_YEAR'] . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['ADDB_COMPANY']) . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['ADDB_PHONE_MAIN']===null?"-":$result_sqlsvr_detail['ADDB_PHONE_MAIN']) . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['ADDB_SEARCH']===null?"-":$result_sqlsvr_detail['ADDB_SEARCH']) . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['ADDB_ADDB_1']) . "  "
        . str_replace(",", "^", $result_sqlsvr_detail['ADDB_ADDB_2']) . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['ADDB_ADDB_3']) . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['SKU_CODE']) . ",";
    $data .= str_replace(",", "^", $result_sqlsvr_detail['SKU_NAME']) . ",";
    $data .= $TRD_QTY . ",";
    $data .= $result_sqlsvr_detail['TRD_B_AMT'] . "\n";

}

echo "\xEF\xBB\xBF"; // UTF-8 BOM
$data = $data;
echo $data;


exit();