<?php
date_default_timezone_set('Asia/Bangkok');

$customer_name = $_POST["customer_name"];
$car_no = $_POST["car_no"];


//$my_file = fopen("Sale_D-CP.txt", "w") or die("Unable to open file!");
//fwrite($my_file, $branch . "-" .$month . "-" .$year . " myCheck  = " . $myCheck);
//fclose($my_file);


$filename = "Data_Customer_History" . "-" . date('m/d/Y H:i:s', time()) . ".csv";

@header('Content-type: text/csv; charset=UTF-8');
@header('Content-Encoding: UTF-8');
@header("Content-Disposition: attachment; filename=" . $filename);

$select_query_daily = " SELECT 
TRANSTKD.TRD_KEY , 
ADDRBOOK.ADDB_KEY , 
ADDRBOOK.ADDB_BRANCH , 
ADDRBOOK.ADDB_SEARCH ,
ADDRBOOK.ADDB_ADDB_1 , 
ADDRBOOK.ADDB_ADDB_2 , 
ADDRBOOK.ADDB_COMPANY ,
ADDRBOOK.ADDB_PHONE ,
DOCINFO.DI_REF , 
DOCINFO.DI_DATE,
DAY(DI_DATE) AS DI_DAY ,
MONTH(DI_DATE) AS DI_MONTH ,
YEAR(DI_DATE) AS DI_YEAR ,
TRANSTKH.TRH_DI,
SKUMASTER.SKU_CODE ,
SKUMASTER.SKU_NAME ,
TRANSTKD.TRD_QTY,
TRANSTKD.TRD_Q_FREE,
TRANSTKD.TRD_U_PRC,
TRANSTKD.TRD_B_SELL,
TRANSTKD.TRD_B_VAT,
TRANSTKD.TRD_B_AMT

FROM 
ADDRBOOK,
ARADDRESS,
ARDETAIL,
DOCINFO ,
TRANSTKH ,
TRANSTKD ,
SKUMASTER
 
WHERE
ADDRBOOK.ADDB_COMPANY like '%" . $customer_name . "%' AND
ADDRBOOK.ADDB_SEARCH like '%" . $car_no . "%' AND
(ADDRBOOK.ADDB_KEY = ARADDRESS.ARA_ADDB) AND 
(ARDETAIL.ARD_AR = ARADDRESS.ARA_AR) AND 
(DOCINFO.DI_KEY = ARDETAIL.ARD_DI) AND 
(DOCINFO.DI_KEY = TRANSTKH.TRH_DI) AND 
(TRANSTKH.TRH_KEY = TRANSTKD.TRD_TRH) AND 
(TRANSTKD.TRD_SKU = SKUMASTER.SKU_KEY)

ORDER BY ADDRBOOK.ADDB_COMPANY , TRD_KEY DESC , SKUMASTER.SKU_CODE ";


include('../config/connect_db.php');


$data = "ลำดับที่,เลขที่เอกสาร,วันที่,ชื่อลูกค้า,ทะเบียนรถ,ยี่ห้อรถ/รุ่น,รหัสสินค้า,ชื่อสินค้า,จำนวน,จำนวนเงิน\n";

$query = $conn->prepare($select_query_daily);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
$line_no = 0;
if ($query->rowCount() >= 1) {
    foreach ($results as $result) {
        $TRD_QTY = $row['TRD_Q_FREE'] > 0 ? $row['TRD_QTY'] = $row['TRD_QTY'] + $row['TRD_Q_FREE'] : $row['TRD_QTY'];
        $line_no++;
        $data .= " " . $line_no . ",";
        $data .= " " . $result->$row['DI_REF'] . ",";
        $data .= " " . $row['DI_DAY'] . "/" . $row['DI_MONTH'] . "/" . $row['DI_YEAR'];
        $data .= " " . $result->$row['ADDB_COMPANY'] . "  " . $row['ADDB_PHONE']. ",";
        $data .= " " . $result->$row['ADDB_SEARCH'] . ",";
        $data .= " " . $result->$row['SKU_CODE'] . ",";
        $data .= " " . $result->$TRD_QTY . ",";
        $data .= " " . $result->TRD_B_AMT . "\n";

        //$data .= str_replace(",", "^", $row['WL_CODE']) . "\n";
    }

}

$data = iconv("utf-8", "tis-620", $data);
echo $data;

exit();