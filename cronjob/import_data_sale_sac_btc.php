<?php

ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");

include('../cond_file/doc_info_sale_daily_cp.php');
include('../util/month_util.php');


$DT_DOCCODE_MINUS1 = "IC";
$DT_DOCCODE_MINUS2 = "IIS";

$str_doc1 = array("CCS6","CCS7","DDS5","IC5","IC6","IIS5","IIS6","IV3");

$str_group1 = array("1SAC03","3SAC02","4SAC02","1SAC04","1SAC02","5SAC02","1SAC12","1SAC13","2SAC02","1SAC01","1SAC14","1SAC07","3SAC03","4SAC03","1SAC08","3SAC05","4SAC05","4SAC01","3SAC04","3SAC01","1SAC11","2SAC03","2SAC08","2SAC10","2SAC06");
$str_group2 = array("8SAC11","TA01-001","8CPA01-001","8CPA01-002","8SAC09","8BTCA01-001","8BTCA01-002");
$str_group3 = array("9SA01","999-07","999-14","999-08");
$str_group4 = array("SAC08");

echo "Today is " . date("Y/m/d");
echo "\n\r" . date("Y/m/d", strtotime("yesterday"));

$query_daily_cond_ext = " AND (DOCTYPE.DT_DOCCODE in ('CCS6','CCS7','DDS5','IC5','IC6','IIS5','IIS6','IV3')) ";

//$query_year = " AND DI_DATE BETWEEN '" . date("Y/m/d", strtotime("yesterday")) . "' AND '" . date("Y/m/d") . "'";

//$query_year = " AND DI_DATE BETWEEN '2018/01/01' AND '2023/12/31'";
$query_year = " AND DI_DATE BETWEEN '2022/01/01' AND '" . date("Y/m/d") . "'";

//$query_year = " AND DI_DATE BETWEEN '2022/08/21' AND '" . date("Y/m/d") . "'";

$sql_sqlsvr = $select_query_daily . $select_query_daily_cond . $query_daily_cond_ext . $query_year . $select_query_daily_order;

//$myfile = fopen("qry_file_mssql_server.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);


/*
 select * from ims_product_sale_sac
    order by
        STR_TO_DATE(DI_DATE, '%m/%d/%Y') desc
 */

$insert_data = "";
$update_data = "";

$res = "";
$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

$sql_find = "SELECT COUNT(*) FROM ims_product_sale_sac_btc WHERE DI_KEY = :DI_KEY AND DI_REF = :DI_REF AND DI_DATE = :DI_DATE AND DT_DOCCODE = :DT_DOCCODE AND TRD_SEQ = :TRD_SEQ";
$stmt_find = $conn->prepare($sql_find);

$sql_update = "UPDATE ims_product_sale_sac_btc SET AR_CODE=:AR_CODE,AR_NAME=:AR_NAME,SLMN_CODE=:SLMN_CODE,SLMN_NAME=:SLMN_NAME,SKU_CODE=:SKU_CODE,SKU_NAME=:SKU_NAME,SKU_CAT=:SKU_CAT,ICCAT_CODE=:ICCAT_CODE,ICCAT_NAME=:ICCAT_NAME,TRD_QTY=:TRD_QTY,TRD_Q_FREE=:TRD_Q_FREE,TRD_U_PRC=:TRD_U_PRC,TRD_DSC_KEYINV=:TRD_DSC_KEYINV,TRD_B_SELL=:TRD_B_SELL,TRD_B_VAT=:TRD_B_VAT,TRD_G_KEYIN=:TRD_G_KEYIN,WL_CODE=:WL_CODE,BRANCH=:BRANCH,BRN_CODE=:BRN_CODE,BRN_NAME=:BRN_NAME,DI_TIME_CHK=:DI_TIME_CHK,PGROUP=:PGROUP,DI_ACTIVE=:DI_ACTIVE,DI_REMARK=:DI_REMARK WHERE DI_KEY = :DI_KEY AND DI_REF = :DI_REF AND DI_DATE = :DI_DATE AND DT_DOCCODE = :DT_DOCCODE AND TRD_SEQ = :TRD_SEQ";
$stmt_update = $conn->prepare($sql_update);

$sql_insert = "INSERT INTO ims_product_sale_sac_btc (DI_KEY,DI_REF,DI_DATE,DI_MONTH,DI_MONTH_NAME,DI_YEAR,AR_CODE,AR_NAME,SLMN_CODE,SLMN_NAME,SKU_CODE,SKU_NAME,SKU_CAT,ICCAT_CODE,ICCAT_NAME,TRD_QTY,TRD_Q_FREE,TRD_U_PRC,TRD_DSC_KEYINV,TRD_B_SELL,TRD_B_VAT,TRD_G_KEYIN,WL_CODE,BRANCH,DT_DOCCODE,TRD_SEQ,BRN_CODE,BRN_NAME,DI_TIME_CHK,PGROUP,DI_ACTIVE,DI_REMARK) VALUES (:DI_KEY,:DI_REF,:DI_DATE,:DI_MONTH,:DI_MONTH_NAME,:DI_YEAR,:AR_CODE,:AR_NAME,:SLMN_CODE,:SLMN_NAME,:SKU_CODE,:SKU_NAME,:SKU_CAT,:ICCAT_CODE,:ICCAT_NAME,:TRD_QTY,:TRD_Q_FREE,:TRD_U_PRC,:TRD_DSC_KEYINV,:TRD_B_SELL,:TRD_B_VAT,:TRD_G_KEYIN,:WL_CODE,:BRANCH,:DT_DOCCODE,:TRD_SEQ,:BRN_CODE,:BRN_NAME,:DI_TIME_CHK,:PGROUP,:DI_ACTIVE,:DI_REMARK)";
$stmt_insert = $conn->prepare($sql_insert);

$conn->beginTransaction();
$count_insert = 0;
$count_update = 0;

try {
    while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $DT_DOCCODE = $result_sqlsvr["DT_DOCCODE"];
        $ICCAT_CODE = $result_sqlsvr["ICCAT_CODE"];

        $branch = "";
        if (in_array($DT_DOCCODE, $str_doc1)) $branch = "BTC";

        if (($result_sqlsvr['DT_PROPERTIES'] == 308) || ($result_sqlsvr['DT_PROPERTIES'] == 337)) {
            $TRD_QTY = (double)$result_sqlsvr["TRD_QTY"] > 0 ? "-" . $result_sqlsvr["TRD_QTY"] : $result_sqlsvr["TRD_QTY"];
            $TRD_U_PRC = (double)$result_sqlsvr["TRD_U_PRC"] > 0 ? "-" . $result_sqlsvr["TRD_U_PRC"] : $result_sqlsvr["TRD_U_PRC"];
            $TRD_DSC_KEYINV = (double)$result_sqlsvr["TRD_DSC_KEYINV"] > 0 ? "-" . $result_sqlsvr["TRD_DSC_KEYINV"] : $result_sqlsvr["TRD_DSC_KEYINV"];
            $TRD_B_SELL = (double)$result_sqlsvr["TRD_B_SELL"] > 0 ? "-" . $result_sqlsvr["TRD_B_SELL"] : $result_sqlsvr["TRD_B_SELL"];
            $TRD_B_VAT = (double)$result_sqlsvr["TRD_B_VAT"] > 0 ? "-" . $result_sqlsvr["TRD_B_VAT"] : $result_sqlsvr["TRD_B_VAT"];
            $TRD_G_KEYIN = (double)$result_sqlsvr["TRD_G_KEYIN"] > 0 ? "-" . $result_sqlsvr["TRD_G_KEYIN"] : $result_sqlsvr["TRD_G_KEYIN"];
        } else {
            $TRD_QTY = $result_sqlsvr["TRD_QTY"];
            $TRD_U_PRC = $result_sqlsvr["TRD_U_PRC"];
            $TRD_DSC_KEYINV = $result_sqlsvr["TRD_DSC_KEYINV"];
            $TRD_B_SELL = $result_sqlsvr["TRD_B_SELL"];
            $TRD_B_VAT = $result_sqlsvr["TRD_B_VAT"];
            $TRD_G_KEYIN = $result_sqlsvr["TRD_G_KEYIN"];
        }

        $p_group = "";
        if (in_array($ICCAT_CODE, $str_group1)) $p_group = "P1";
        if (in_array($ICCAT_CODE, $str_group2)) $p_group = "P2";
        if (in_array($ICCAT_CODE, $str_group3)) $p_group = "P3";
        if (in_array($ICCAT_CODE, $str_group4)) $p_group = "P4";

        $stmt_find->execute([
            ':DI_KEY' => $result_sqlsvr["DI_KEY"],
            ':DI_REF' => $result_sqlsvr["DI_REF"],
            ':DI_DATE' => $result_sqlsvr["DI_DATE"],
            ':DT_DOCCODE' => $result_sqlsvr["DT_DOCCODE"],
            ':TRD_SEQ' => $result_sqlsvr["TRD_SEQ"]
        ]);

        if ($stmt_find->fetchColumn() > 0) {
            $stmt_update->execute([
                ':AR_CODE' => $result_sqlsvr["AR_CODE"],
                ':AR_NAME' => $result_sqlsvr["AR_NAME"],
                ':SLMN_CODE' => $result_sqlsvr["SLMN_CODE"],
                ':SLMN_NAME' => $result_sqlsvr["SLMN_NAME"],
                ':SKU_CODE' => $result_sqlsvr["SKU_CODE"],
                ':SKU_NAME' => $result_sqlsvr["SKU_NAME"],
                ':SKU_CAT' => $result_sqlsvr["ICCAT_CODE"],
                ':ICCAT_CODE' => $result_sqlsvr["ICCAT_CODE"],
                ':ICCAT_NAME' => $result_sqlsvr["ICCAT_NAME"],
                ':TRD_QTY' => $TRD_QTY,
                ':TRD_Q_FREE' => $result_sqlsvr["TRD_Q_FREE"],
                ':TRD_U_PRC' => $TRD_U_PRC,
                ':TRD_DSC_KEYINV' => $TRD_DSC_KEYINV,
                ':TRD_B_SELL' => $TRD_B_SELL,
                ':TRD_B_VAT' => $TRD_B_VAT,
                ':TRD_G_KEYIN' => $TRD_G_KEYIN,
                ':WL_CODE' => $result_sqlsvr["WL_CODE"],
                ':BRANCH' => $branch,
                ':BRN_CODE' => $result_sqlsvr["BRN_CODE"],
                ':BRN_NAME' => $result_sqlsvr["BRN_NAME"],
                ':DI_TIME_CHK' => $result_sqlsvr["DI_TIME_CHK"],
                ':PGROUP' => $p_group,
                ':DI_ACTIVE' => $result_sqlsvr["DI_ACTIVE"],
                ':DI_REMARK' => $result_sqlsvr["DI_REMARK"],
                ':DI_KEY' => $result_sqlsvr["DI_KEY"],
                ':DI_REF' => $result_sqlsvr["DI_REF"],
                ':DI_DATE' => $result_sqlsvr["DI_DATE"],
                ':DT_DOCCODE' => $result_sqlsvr["DT_DOCCODE"],
                ':TRD_SEQ' => $result_sqlsvr["TRD_SEQ"]
            ]);
            $count_update++;
        } else {
            $month_name = isset($month_arr[$result_sqlsvr["DI_MONTH"]]) ? $month_arr[$result_sqlsvr["DI_MONTH"]] : "";

            $stmt_insert->execute([
                ':DI_KEY' => $result_sqlsvr["DI_KEY"],
                ':DI_REF' => $result_sqlsvr["DI_REF"],
                ':DI_DATE' => $result_sqlsvr["DI_DATE"],
                ':DI_MONTH' => $result_sqlsvr["DI_MONTH"],
                ':DI_MONTH_NAME' => $month_name,
                ':DI_YEAR' => $result_sqlsvr["DI_YEAR"],
                ':AR_CODE' => $result_sqlsvr["AR_CODE"],
                ':AR_NAME' => $result_sqlsvr["AR_NAME"],
                ':SLMN_CODE' => $result_sqlsvr["SLMN_CODE"],
                ':SLMN_NAME' => $result_sqlsvr["SLMN_NAME"],
                ':SKU_CODE' => $result_sqlsvr["SKU_CODE"],
                ':SKU_NAME' => $result_sqlsvr["SKU_NAME"],
                ':SKU_CAT' => $result_sqlsvr["ICCAT_CODE"],
                ':ICCAT_CODE' => $result_sqlsvr["ICCAT_CODE"],
                ':ICCAT_NAME' => $result_sqlsvr["ICCAT_NAME"],
                ':TRD_QTY' => $TRD_QTY,
                ':TRD_Q_FREE' => $result_sqlsvr["TRD_Q_FREE"],
                ':TRD_U_PRC' => $TRD_U_PRC,
                ':TRD_DSC_KEYINV' => $TRD_DSC_KEYINV,
                ':TRD_B_SELL' => $TRD_B_SELL,
                ':TRD_B_VAT' => $TRD_B_VAT,
                ':TRD_G_KEYIN' => $TRD_G_KEYIN,
                ':WL_CODE' => $result_sqlsvr["WL_CODE"],
                ':BRANCH' => $branch,
                ':DT_DOCCODE' => $DT_DOCCODE,
                ':TRD_SEQ' => $result_sqlsvr["TRD_SEQ"],
                ':BRN_CODE' => $result_sqlsvr["BRN_CODE"],
                ':BRN_NAME' => $result_sqlsvr["BRN_NAME"],
                ':DI_TIME_CHK' => $result_sqlsvr["DI_TIME_CHK"],
                ':PGROUP' => $p_group,
                ':DI_ACTIVE' => $result_sqlsvr["DI_ACTIVE"],
                ':DI_REMARK' => $result_sqlsvr["DI_REMARK"]
            ]);
            $count_insert++;
        }
    }
    $conn->commit();
    echo "sale_sac_btc import finished. Insert: $count_insert, Update: $count_update\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in sale_sac_btc import: " . $e->getMessage() . "\n";
}

$conn_sqlsvr = null;
