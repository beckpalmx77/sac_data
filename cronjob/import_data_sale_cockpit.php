<?php

ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");

include('../cond_file/doc_info_sale_daily_cp.php');
include('../util/month_util.php');

$str_doc1 = array("30", "CS4", "CS5", "DS4", "IS3", "IS4", "ISC3", "ISC4");
$str_doc2 = array("CS.8", "CS.9", "IC.3", "IC.4", "IS.3", "IS.4", "S.5", "S.6");
$str_doc3 = array("CS.6", "CS.7", "IC.1", "IC.2", "IS.1", "IS.2", "S.1", "S.2");
$str_doc4 = array("CS.2", "CS.3", "IC.5", "IC.6", "IS.5", "IS.6", "S.3", "S.4");

//$group1 = "6SAC08 2SAC01 2SAC09 2SAC11 2SAC02 2SAC06 2SAC05 2SAC04 2SAC03 2SAC12 2SAC07 2SAC08 2SAC10 2SAC13 2SAC14 2SAC15 3SAC03 1SAC10";
//$group2 = "5SAC02 8SAC11 5SAC01 TA01-001 8SAC09 TA01-003 8CPA01-002 8BTCA01-002 8CPA01-001 8BTCA01-001";
//$group3 = "9SA01 999-13 999-07 999-08 TATA-004";
//$group4 = "TATA-003 SAC08 10SAC12";

$str_group1 = array("6SAC08","2SAC01","2SAC09","2SAC11","2SAC02","2SAC06","2SAC05","2SAC04","2SAC03","2SAC12","2SAC07","2SAC08","2SAC10","2SAC13","2SAC14","2SAC15","2SAC16","2SAC17","2SAC18","2SAC19","2SAC20","2SAC21","3SAC03","1SAC10","1SAC04");
$str_group2 = array("5SAC02","8SAC11","5SAC01","TA01-001","8SAC09","TA01-003","8CPA01-002","8BTCA01-002","8CPA01-001","8BTCA01-001");
$str_group3 = array("9SA01","999-13","999-07","999-08","TATA-004","999-14");
$str_group4 = array("TATA-003","SAC08","10SAC12");

echo "Today is " . date("Y/m/d") . "\n\r" ;
echo "Yesterday is " . date("Y/m/d", strtotime("yesterday")) . "\n\r" ;
echo "Host - " . $host . "\n\r";

$query_daily_cond_ext = " AND (DOCTYPE.DT_DOCCODE in ('30','CS4','CS5','DS4','IS3','IS4','ISC3','ISC4','CS.8','CS.9','IC.3','IC.4','IS.3','IS.4','S.5','S.6','CS.6','CS.7','IC.1','IC.2','IS.1','IS.2','S.1','S.2','CS.2','CS.3','IC.5','IC.6','IS.5','IS.6','S.3','S.4')) ";

$query_year = " AND DI_DATE BETWEEN '" . date("Y/m/d", strtotime("yesterday")) . "' AND '" . date("Y/m/d") . "'";
//$query_year = " AND DI_DATE BETWEEN '2017/01/01' AND '2023/07/31'";
//$query_year = " AND DI_DATE BETWEEN '2022/06/27' AND '" . date("Y/m/d") . "'";

$sql_sqlsvr = $select_query_daily . $select_query_daily_cond . $query_daily_cond_ext . $query_year . $select_query_daily_order;

echo $sql_sqlsvr;

/*
$myfile = fopen("qry_file_mssql_server.txt", "w") or die("Unable to open file!");
fwrite($myfile, $sql_sqlsvr);
fclose($myfile);
*/

$insert_data = "";
$update_data = "";

$res = "";

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {

    $ICCAT_CODE = "";

    $DT_DOCCODE = $result_sqlsvr["DT_DOCCODE"];
    $ICCAT_CODE = $result_sqlsvr["ICCAT_CODE"];

    $TRD_QTY = $result_sqlsvr['TRD_Q_FREE'] > 0 ? $result_sqlsvr['TRD_QTY'] + $result_sqlsvr['TRD_Q_FREE'] : $result_sqlsvr['TRD_QTY'];

    $branch = "";

    if (in_array($DT_DOCCODE, $str_doc1)) {
        $branch = "CP-340";
    }

    if (in_array($DT_DOCCODE, $str_doc2)) {
        $branch = "CP-BY";
    }

    if (in_array($DT_DOCCODE, $str_doc3)) {
        $branch = "CP-RP";
    }

    if (in_array($DT_DOCCODE, $str_doc4)) {
        $branch = "CP-BB";
    }

    echo "[ " . $DT_DOCCODE . " | " . $branch . " ]" . "\n\r";

    $res = $res . $result_sqlsvr["DI_REF"] . "  *** " . $result_sqlsvr["DT_DOCCODE"] . " *** " . "\n\r";

    //$myfile = fopen("sql_get_DATA.txt", "w") or die("Unable to open file!");
    //fwrite($myfile, "[" . $res) ;
    //fclose($myfile);


    $p_group = "";

    /*
        if (strpos($group1, $ICCAT_CODE) !== false) {
            $p_group = "P1";
        }

        if (strpos($group2, $ICCAT_CODE) !== false) {
            $p_group = "P2";
        }

        if (strpos($group3, $ICCAT_CODE) !== false) {
            $p_group = "P3";
        }

        if (strpos($group4, $ICCAT_CODE) !== false) {
            $p_group = "P4";
        }
    */

    if (in_array($ICCAT_CODE, $str_group1)) {
        $p_group = "P1";
    }

    if (in_array($ICCAT_CODE, $str_group2)) {
        $p_group = "P2";
    }

    if (in_array($ICCAT_CODE, $str_group3)) {
        $p_group = "P3";
    }

    if (in_array($ICCAT_CODE, $str_group4)) {
        $p_group = "P4";
    }

    $sql_find = "SELECT * FROM ims_product_sale_cockpit "
        . " WHERE DI_KEY = '" . $result_sqlsvr["DI_KEY"]
        . "' AND DI_REF = '" . $result_sqlsvr["DI_REF"]
        . "' AND DI_DATE = '" . $result_sqlsvr["DI_DATE"]
        . "' AND DT_DOCCODE = '" . $result_sqlsvr["DT_DOCCODE"]
        . "' AND TRD_SEQ = '" . $result_sqlsvr["TRD_SEQ"] . "'";

    //echo $sql_find . "\n\r";

    $nRows = $conn->query($sql_find)->fetchColumn();
    if ($nRows > 0) {

        $sql_update = " UPDATE ims_product_sale_cockpit  SET AR_CODE=:AR_CODE,AR_NAME=:AR_NAME,SLMN_CODE=:SLMN_CODE,SLMN_NAME=:SLMN_NAME
,SKU_CODE=:SKU_CODE,SKU_NAME=:SKU_NAME,SKU_CAT=:SKU_CAT,ICCAT_CODE=:ICCAT_CODE,ICCAT_NAME=:ICCAT_NAME,TRD_QTY=:TRD_QTY,TRD_Q_FREE=:TRD_Q_FREE,TRD_U_PRC=:TRD_U_PRC
,TRD_DSC_KEYINV=:TRD_DSC_KEYINV,TRD_B_SELL=:TRD_B_SELL
,TRD_B_VAT=:TRD_B_VAT,TRD_G_KEYIN=:TRD_G_KEYIN,WL_CODE=:WL_CODE,BRANCH=:BRANCH,BRN_CODE=:BRN_CODE
,BRN_NAME=:BRN_NAME,DI_TIME_CHK=:DI_TIME_CHK,PGROUP=:PGROUP,DI_ACTIVE=:DI_ACTIVE    
        WHERE DI_KEY = :DI_KEY         
        AND DI_REF  = :DI_REF
        AND DI_DATE = :DI_DATE
        AND DT_DOCCODE = :DT_DOCCODE
        AND TRD_SEQ = :TRD_SEQ ";

        $query = $conn->prepare($sql_update);
        $query->bindParam(':AR_CODE', $result_sqlsvr["AR_CODE"], PDO::PARAM_STR);
        $query->bindParam(':AR_NAME', $result_sqlsvr["AR_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_CODE', $result_sqlsvr["SLMN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_NAME', $result_sqlsvr["SLMN_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CODE', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SKU_NAME', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CAT', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query->bindParam(':ICCAT_CODE', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query->bindParam(':ICCAT_NAME', $result_sqlsvr["ICCAT_NAME"], PDO::PARAM_STR);
        //$query->bindParam(':TRD_QTY', $result_sqlsvr["TRD_QTY"], PDO::PARAM_STR);
        $query->bindParam(':TRD_QTY', $TRD_QTY, PDO::PARAM_STR);
        $query->bindParam(':TRD_Q_FREE', $result_sqlsvr["TRD_Q_FREE"], PDO::PARAM_STR);
        $query->bindParam(':TRD_U_PRC', $result_sqlsvr["TRD_U_PRC"], PDO::PARAM_STR);
        $query->bindParam(':TRD_DSC_KEYINV', $result_sqlsvr["TRD_DSC_KEYINV"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_SELL', $result_sqlsvr["TRD_B_SELL"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_VAT', $result_sqlsvr["TRD_B_VAT"], PDO::PARAM_STR);
        $query->bindParam(':TRD_G_KEYIN', $result_sqlsvr["TRD_G_KEYIN"], PDO::PARAM_STR);
        $query->bindParam(':WL_CODE', $result_sqlsvr["WL_CODE"], PDO::PARAM_STR);

        $query->bindParam(':BRANCH', $branch, PDO::PARAM_STR);
        $query->bindParam(':BRN_CODE', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':BRN_NAME', $result_sqlsvr["BRN_NAME"], PDO::PARAM_STR);
        $query->bindParam(':DI_TIME_CHK', $result_sqlsvr["DI_TIME_CHK"], PDO::PARAM_STR);
        $query->bindParam(':PGROUP', $p_group, PDO::PARAM_STR);
        $query->bindParam(':DI_ACTIVE', $result_sqlsvr["DI_ACTIVE"], PDO::PARAM_STR);

        $query->bindParam(':DI_KEY', $result_sqlsvr["DI_KEY"], PDO::PARAM_STR);
        $query->bindParam(':DI_REF', $result_sqlsvr["DI_REF"], PDO::PARAM_STR);
        $query->bindParam(':DI_DATE', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':DT_DOCCODE', $result_sqlsvr["DT_DOCCODE"], PDO::PARAM_STR);
        $query->bindParam(':TRD_SEQ', $result_sqlsvr["TRD_SEQ"], PDO::PARAM_STR);

        $query->execute();

        $update_data .= $result_sqlsvr["DI_DATE"] . ":" . $result_sqlsvr["DI_REF"] . " |- " . $result_sqlsvr["ICCAT_CODE"] . "\n\r";

        echo " UPDATE DATA " . $update_data;

        //$myfile = fopen("update_chk.txt", "w") or die("Unable to open file!");
        //fwrite($myfile, $update_data);
        //fclose($myfile);

    } else {

        $sql = " INSERT INTO ims_product_sale_cockpit (DI_KEY,DI_REF,DI_DATE,DI_MONTH,DI_MONTH_NAME,DI_YEAR
        ,AR_CODE,AR_NAME,SLMN_CODE,SLMN_NAME,SKU_CODE,SKU_NAME,SKU_CAT,ICCAT_CODE,ICCAT_NAME,TRD_QTY,TRD_Q_FREE,TRD_U_PRC
        ,TRD_DSC_KEYINV,TRD_B_SELL,TRD_B_VAT,TRD_G_KEYIN,WL_CODE,BRANCH,DT_DOCCODE,TRD_SEQ,BRN_CODE,BRN_NAME,DI_TIME_CHK,PGROUP,DI_ACTIVE)
        VALUES (:DI_KEY,:DI_REF,:DI_DATE,:DI_MONTH,:DI_MONTH_NAME,:DI_YEAR,:AR_CODE,:AR_NAME,:SLMN_CODE,:SLMN_NAME,:SKU_CODE,:SKU_NAME,:SKU_CAT
        ,:ICCAT_CODE,:ICCAT_NAME,:TRD_QTY,:TRD_Q_FREE,:TRD_U_PRC,:TRD_DSC_KEYINV,:TRD_B_SELL,:TRD_B_VAT,:TRD_G_KEYIN
        ,:WL_CODE,:BRANCH,:DT_DOCCODE,:TRD_SEQ,:BRN_CODE,:BRN_NAME,:DI_TIME_CHK,:PGROUP,:DI_ACTIVE) ";
        $query = $conn->prepare($sql);
        $query->bindParam(':DI_KEY', $result_sqlsvr["DI_KEY"], PDO::PARAM_STR);
        $query->bindParam(':DI_REF', $result_sqlsvr["DI_REF"], PDO::PARAM_STR);
        $query->bindParam(':DI_DATE', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':DI_MONTH', $result_sqlsvr["DI_MONTH"], PDO::PARAM_STR);
        $query->bindParam(':DI_MONTH_NAME', $month_arr[$result_sqlsvr["DI_MONTH"]], PDO::PARAM_STR);
        $query->bindParam(':DI_YEAR', $result_sqlsvr["DI_YEAR"], PDO::PARAM_STR);
        $query->bindParam(':AR_CODE', $result_sqlsvr["AR_CODE"], PDO::PARAM_STR);
        $query->bindParam(':AR_NAME', $result_sqlsvr["AR_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_CODE', $result_sqlsvr["SLMN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_NAME', $result_sqlsvr["SLMN_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CODE', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SKU_NAME', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CAT', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query->bindParam(':ICCAT_CODE', $result_sqlsvr["ICCAT_CODE"], PDO::PARAM_STR);
        $query->bindParam(':ICCAT_NAME', $result_sqlsvr["ICCAT_NAME"], PDO::PARAM_STR);
        //$query->bindParam(':TRD_QTY', $result_sqlsvr["TRD_QTY"], PDO::PARAM_STR);
        $query->bindParam(':TRD_QTY', $TRD_QTY, PDO::PARAM_STR);
        $query->bindParam(':TRD_Q_FREE', $result_sqlsvr["TRD_Q_FREE"], PDO::PARAM_STR);
        $query->bindParam(':TRD_U_PRC', $result_sqlsvr["TRD_U_PRC"], PDO::PARAM_STR);
        $query->bindParam(':TRD_DSC_KEYINV', $result_sqlsvr["TRD_DSC_KEYINV"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_SELL', $result_sqlsvr["TRD_B_SELL"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_VAT', $result_sqlsvr["TRD_B_VAT"], PDO::PARAM_STR);
        $query->bindParam(':TRD_G_KEYIN', $result_sqlsvr["TRD_G_KEYIN"], PDO::PARAM_STR);
        $query->bindParam(':WL_CODE', $result_sqlsvr["WL_CODE"], PDO::PARAM_STR);

        $query->bindParam(':BRANCH', $branch, PDO::PARAM_STR);

        $query->bindParam(':DT_DOCCODE', $DT_DOCCODE, PDO::PARAM_STR);

        $query->bindParam(':TRD_SEQ', $result_sqlsvr["TRD_SEQ"], PDO::PARAM_STR);

        $query->bindParam(':BRN_CODE', $result_sqlsvr["BRN_CODE"], PDO::PARAM_STR);

        $query->bindParam(':BRN_NAME', $result_sqlsvr["BRN_NAME"], PDO::PARAM_STR);

        $query->bindParam(':DI_TIME_CHK', $result_sqlsvr["DI_TIME_CHK"], PDO::PARAM_STR);

        $query->bindParam(':PGROUP', $p_group, PDO::PARAM_STR);

        $query->bindParam(':DI_ACTIVE', $result_sqlsvr["DI_ACTIVE"], PDO::PARAM_STR);



        $query->execute();

        $lastInsertId = $conn->lastInsertId();

        if ($lastInsertId) {
            $insert_data .= $result_sqlsvr["DI_DATE"] . ":" . $result_sqlsvr["DI_REF"] . " | ";
            echo " Save OK " . $insert_data . "\n\r";
        } else {
            echo " Error ";
        }

    }

}

$conn_sqlsvr = null;

