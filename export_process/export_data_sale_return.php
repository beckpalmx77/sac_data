<?php
date_default_timezone_set('Asia/Bangkok');

$filename= "Data_Sale_Return-" . date('m/d/Y H:i:s', time()) . ".csv";

@header('Content-type: text/csv; charset=UTF-8');
@header('Content-Encoding: UTF-8');
@header("Content-Disposition: attachment; filename=" . $filename);

include('config/connect_sqlserver.php');
include('cond_file/doc_info_credit_sale.php');
include('cond_file/doc_info_return_product.php');

$doc_date_start = $_POST['doc_date_start'];
$doc_date_to = $_POST['doc_date_to'];

for ($loop=1;$loop<=2;$loop++) {

    if ($loop===1) {

        $String_Sql = $select_query_sale . $sql_cond_sale . " AND DI_DATE BETWEEN '" . $doc_date_start . "' AND '" . $doc_date_to . "' "
                      . $sql_order_sale;

        $data = "DI_REF,DI_DATE,AR_CODE,AR_NAME,SLMN_CODE,SLMN_NAME,SKU_CODE,SKU_NAME,TRD_QTY,TRD_Q_FREE,TRD_U_PRC,TRD_G_KEYIN,TRD_G_SELL,TRD_G_VAT,WL_CODE,ARCD_NAME\n";

    } else {

        $String_Sql = $select_query_return . $sql_cond_return . " AND DI_DATE BETWEEN '" . $doc_date_start . "' AND '" . $doc_date_to . "' "
            . $sql_order_return;

    }

    $query = $conn_sqlsvr->prepare($String_Sql);
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $data .= $row['DI_REF'] . ",";
        $data .= $row['DI_DATE'] . ",";
        $data .= str_replace(",","^",$row['AR_CODE']) . ",";
        $data .= str_replace(",","^",$row['AR_NAME']) . ",";
        $data .= str_replace(",","^",$row['SLMN_CODE']) . ",";
        $data .= str_replace(",","^",$row['SLMN_NAME']) . ",";
        $data .= str_replace(",","^",$row['SKU_CODE']) . ",";
        $data .= str_replace(",","^",$row['SKU_NAME']) . ",";
        $data .= $row['TRD_QTY'] . ",";
        $data .= $row['TRD_Q_FREE'] . ",";
        $data .= $row['TRD_U_PRC'] . ",";
        $data .= $row['TRD_G_KEYIN'] . ",";
        $data .= $row['TRD_G_SELL'] . ",";
        $data .= $row['TRD_G_VAT'] . ",";
        $data .= str_replace(",","^",$row['WL_CODE']) . ",";
        $data .= str_replace(",","^",$row['ARCD_NAME']) . "\n";

    }
}

$data = iconv("utf-8", "tis-620", $data );
echo $data;

exit();