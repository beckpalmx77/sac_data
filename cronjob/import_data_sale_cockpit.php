<?php

ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_sqlserver.php");
include("../config/connect_db.php");

include('../cond_file/doc_info_sale_daily_cp.php');


$query_daily_cond_ext = " AND (DOCTYPE.DT_DOCCODE in ('30','CS4','CS5','DS4','IS3','IS4','ISC3','ISC4','CS.8','CS.9','IC.3','IC.4','IS.3','IS.4','S.5','S.6','CS.6','CS.7','IC.1','IC.2','IS.1','IS.2','S.1','S.2','CS.2','CS.3','IC.5','IC.6','IS.5','IS.6','S.3','S.4')) ";

$query_year = " AND YEAR(DI_DATE) = '2022' ";

$sql_sqlsvr = $select_query_daily . $select_query_daily_cond . $query_daily_cond_ext . $query_year . $select_query_daily_order;

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_sqlsvr);
//fclose($myfile);

$update_data = "";

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$return_arr = array();

while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {

    $sql_find = "SELECT * FROM ims_product_sale_cockpit WHERE DI_KEY = '" . $result_sqlsvr["DI_KEY"]
        . "' AND DI_REF = '" . $result_sqlsvr["DI_REF"]
        . "' AND DI_DATE = '" . $result_sqlsvr["DI_DATE"]
        . "' AND DT_DOCCODE = '" . $result_sqlsvr["DT_DOCCODE"]
        . "' AND TRD_QTY = '" . $result_sqlsvr["TRD_QTY"]
        . "' AND WL_CODE = '" . $result_sqlsvr["WL_CODE"]
        . "' AND SKU_CODE = '" . $result_sqlsvr["SKU_CODE"] . "'";

    $nRows = $conn->query($sql_find)->fetchColumn();
    if ($nRows > 0) {

        /*

        $sql_update = " UPDATE ims_product_sale_cockpit SET DI_REF=:DI_REF,DI_DATE=:DI_DATE,DI_MONTH=:DI_MONTH,DI_YEAR=:DI_YEAR,AR_CODE=:AR_CODE,AR_NAME=:AR_NAME,SLMN_CODE=:SLMN_CODE,SLMN_NAME=:SLMN_NAME
,SKU_CODE=:SKU_CODE,SKU_NAME=:SKU_NAME,SKU_CAT=:SKU_CAT,ICCAT_NAME=:ICCAT_NAME,TRD_QTY=:TRD_QTY,TRD_U_PRC=:TRD_U_PRC,TRD_DSC_KEYINV=:TRD_DSC_KEYINV,TRD_B_SELL=:TRD_B_SELL
,TRD_B_VAT=:TRD_B_VAT,TRD_G_KEYIN=:TRD_G_KEYIN,WL_CODE=:WL_CODE,BRANCH=:BRANCH,DT_DOCCODE=:DT_DOCCODE WHERE DI_KEY = :DI_KEY ";

        $query = $conn->prepare($sql_update);
        $query->bindParam(':DI_REF', $result_sqlsvr["DI_REF"], PDO::PARAM_STR);
        $query->bindParam(':DI_DATE', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':DI_MONTH', $result_sqlsvr["DI_MONTH"], PDO::PARAM_STR);
        $query->bindParam(':DI_YEAR', $result_sqlsvr["DI_YEAR"], PDO::PARAM_STR);
        $query->bindParam(':AR_CODE', $result_sqlsvr["AR_CODE"], PDO::PARAM_STR);
        $query->bindParam(':AR_NAME', $result_sqlsvr["AR_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_CODE', $result_sqlsvr["SLMN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_NAME', $result_sqlsvr["SLMN_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CODE', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SKU_NAME', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CAT', $result_sqlsvr["SKU_CAT"], PDO::PARAM_STR);
        $query->bindParam(':ICCAT_NAME', $result_sqlsvr["ICCAT_NAME"], PDO::PARAM_STR);
        $query->bindParam(':TRD_QTY', $result_sqlsvr["TRD_QTY"], PDO::PARAM_STR);
        $query->bindParam(':TRD_U_PRC', $result_sqlsvr["TRD_U_PRC"], PDO::PARAM_STR);
        $query->bindParam(':TRD_DSC_KEYINV', $result_sqlsvr["TRD_DSC_KEYINV"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_SELL', $result_sqlsvr["TRD_B_SELL"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_VAT', $result_sqlsvr["TRD_B_VAT"], PDO::PARAM_STR);
        $query->bindParam(':TRD_G_KEYIN', $result_sqlsvr["TRD_G_KEYIN"], PDO::PARAM_STR);
        $query->bindParam(':WL_CODE', $result_sqlsvr["WL_CODE"], PDO::PARAM_STR);

        $DT_DOCCODE = $result_sqlsvr["DT_DOCCODE"];

        $branch = "";

        if (preg_match('(30|CS4|CS5|DS4|IS3|IS4|ISC3|ISC4)', $DT_DOCCODE) === 1) {
            $branch = "CP-340";
        } else if (preg_match('(CS.8|CS.9|IC.3|IC.4|IS.3|IS.4|S.5|S.6)', $DT_DOCCODE) === 1) {
            $branch = "CP-BY";
        } else if (preg_match('(CS.6|CS.7|IC.1|IC.2|IS.1|IS.2|S.1|S.2)', $DT_DOCCODE) === 1) {
            $branch = "CP-RP";
        } else if (preg_match('(CS.2|CS.3|IC.5|IC.6|IS.5|IS.6|S.3|S.4)', $DT_DOCCODE) === 1) {
            $branch = "CP-BB";
        }

        $query->bindParam(':BRANCH', $branch, PDO::PARAM_STR);

        $query->bindParam(':DT_DOCCODE', $result_sqlsvr["DT_DOCCODE"], PDO::PARAM_STR);

        $query->bindParam(':DI_KEY', $result_sqlsvr["DI_KEY"], PDO::PARAM_STR);

        $update_data .= $result_sqlsvr["DI_REF"] . " | " ;
        */

        //$query->execute();
        //echo " UPDATE DATA ";
        //$myfile = fopen("update_chk.txt", "w") or die("Unable to open file!");
        //fwrite($myfile, $update_data);
        //fclose($myfile);

    } else {

        $sql = " INSERT INTO ims_product_sale_cockpit(DI_KEY,DI_REF,DI_DATE,DI_MONTH,DI_YEAR,AR_CODE,AR_NAME,SLMN_CODE,SLMN_NAME,SKU_CODE,SKU_NAME,SKU_CAT,ICCAT_NAME,TRD_QTY,TRD_U_PRC,TRD_DSC_KEYINV,TRD_B_SELL,TRD_B_VAT,TRD_G_KEYIN,WL_CODE,BRANCH,DT_DOCCODE)
        VALUES (:DI_KEY,:DI_REF,:DI_DATE,:DI_MONTH,:DI_YEAR,:AR_CODE,:AR_NAME,:SLMN_CODE,:SLMN_NAME,:SKU_CODE,:SKU_NAME,:SKU_CAT
        ,:ICCAT_NAME,:TRD_QTY,:TRD_U_PRC,:TRD_DSC_KEYINV,:TRD_B_SELL,:TRD_B_VAT,:TRD_G_KEYIN,:WL_CODE,:BRANCH,:DT_DOCCODE) ";
        $query = $conn->prepare($sql);
        $query->bindParam(':DI_KEY', $result_sqlsvr["DI_KEY"], PDO::PARAM_STR);
        $query->bindParam(':DI_REF', $result_sqlsvr["DI_REF"], PDO::PARAM_STR);
        $query->bindParam(':DI_DATE', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':DI_MONTH', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':DI_YEAR', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':AR_CODE', $result_sqlsvr["AR_CODE"], PDO::PARAM_STR);
        $query->bindParam(':AR_NAME', $result_sqlsvr["AR_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_CODE', $result_sqlsvr["SLMN_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SLMN_NAME', $result_sqlsvr["SLMN_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CODE', $result_sqlsvr["SKU_CODE"], PDO::PARAM_STR);
        $query->bindParam(':SKU_NAME', $result_sqlsvr["SKU_NAME"], PDO::PARAM_STR);
        $query->bindParam(':SKU_CAT', $result_sqlsvr["SKU_CAT"], PDO::PARAM_STR);
        $query->bindParam(':ICCAT_NAME', $result_sqlsvr["ICCAT_NAME"], PDO::PARAM_STR);
        $query->bindParam(':TRD_QTY', $result_sqlsvr["TRD_QTY"], PDO::PARAM_STR);
        $query->bindParam(':TRD_U_PRC', $result_sqlsvr["TRD_U_PRC"], PDO::PARAM_STR);
        $query->bindParam(':TRD_DSC_KEYINV', $result_sqlsvr["TRD_DSC_KEYINV"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_SELL', $result_sqlsvr["TRD_B_SELL"], PDO::PARAM_STR);
        $query->bindParam(':TRD_B_VAT', $result_sqlsvr["TRD_B_VAT"], PDO::PARAM_STR);
        $query->bindParam(':TRD_G_KEYIN', $result_sqlsvr["TRD_G_KEYIN"], PDO::PARAM_STR);
        $query->bindParam(':WL_CODE', $result_sqlsvr["WL_CODE"], PDO::PARAM_STR);

        $DT_DOCCODE = $result_sqlsvr["DT_DOCCODE"];

        $branch = "";

        if (preg_match('(30|CS4|CS5|DS4|IS3|IS4|ISC3|ISC4)', $DT_DOCCODE) === 1) {
            $branch = "CP-340";
        } else if (preg_match('(CS.8|CS.9|IC.3|IC.4|IS.3|IS.4|S.5|S.6)', $DT_DOCCODE) === 1) {
            $branch = "CP-BY";
        } else if (preg_match('(CS.6|CS.7|IC.1|IC.2|IS.1|IS.2|S.1|S.2)', $DT_DOCCODE) === 1) {
            $branch = "CP-RP";
        } else if (preg_match('(CS.2|CS.3|IC.5|IC.6|IS.5|IS.6|S.3|S.4)', $DT_DOCCODE) === 1) {
            $branch = "CP-BB";
        }

        $query->bindParam(':BRANCH', $branch, PDO::PARAM_STR);

        $query->bindParam(':DT_DOCCODE', $DT_DOCCODE, PDO::PARAM_STR);

        $query->execute();

        $lastInsertId = $conn->lastInsertId();

        if ($lastInsertId) {
            echo " Save OK ";
        } else {
            echo " Error ";
        }

    }

}

$conn_sqlsvr = null;

