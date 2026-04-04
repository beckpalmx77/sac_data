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

$str_group1 = array("6SAC08","2SAC01","2SAC09","2SAC11","2SAC02","2SAC06","2SAC05","2SAC04","2SAC03","2SAC12","2SAC07","2SAC08","2SAC10","2SAC13","2SAC14","2SAC15","2SAC16","2SAC17","2SAC18","2SAC19","2SAC20","2SAC21","3SAC03","1SAC10","1SAC04");
$str_group2 = array("5SAC02","8SAC11","5SAC01","TA01-001","8SAC09","TA01-003","8CPA01-002","8BTCA01-002","8CPA01-001","8BTCA01-001");
$str_group3 = array("9SA01","999-13","999-07","999-08","TATA-004","999-14");
$str_group4 = array("TATA-003","SAC08","10SAC12");

echo "Today is " . date("Y/m/d") . "\n\r" ;
echo "Yesterday is " . date("Y/m/d", strtotime("yesterday")) . "\n\r" ;

$query_daily_cond_ext = " AND (DOCTYPE.DT_DOCCODE in ('30','CS4','CS5','DS4','IS3','IS4','ISC3','ISC4','CS.8','CS.9','IC.3','IC.4','IS.3','IS.4','S.5','S.6','CS.6','CS.7','IC.1','IC.2','IS.1','IS.2','S.1','S.2','CS.2','CS.3','IC.5','IC.6','IS.5','IS.6','S.3','S.4')) ";
$query_year = "";
// $query_year = " AND DI_DATE BETWEEN '" . date("Y/m/d", strtotime("yesterday")) . "' AND '" . date("Y/m/d") . "'";

$sql_sqlsvr = $select_query_daily . $select_query_daily_cond . $query_daily_cond_ext . $query_year . $select_query_daily_order;

$stmt_sqlsvr = $conn_sqlsvr->prepare($sql_sqlsvr);
$stmt_sqlsvr->execute();

$record = 0;

while ($result_sqlsvr = $stmt_sqlsvr->fetch(PDO::FETCH_ASSOC)) {

    // ตรวจสอบว่ามีข้อมูลเดิมอยู่ใน ims_product_sale_cockpit หรือไม่
    $sql_find = "SELECT COUNT(*) FROM ims_product_sale_cockpit "
        . " WHERE DI_KEY = '" . $result_sqlsvr["DI_KEY"]
        . "' AND DI_REF = '" . $result_sqlsvr["DI_REF"]
        . "' AND DI_DATE = '" . $result_sqlsvr["DI_DATE"]
        . "' AND DT_DOCCODE = '" . $result_sqlsvr["DT_DOCCODE"]
        . "' AND TRD_SEQ = '" . $result_sqlsvr["TRD_SEQ"] . "'";

    $nRows = $conn->query($sql_find)->fetchColumn();

    if ($nRows > 0) {
        // อัปเดตเฉพาะฟิลด์ DI_REMARK เท่านั้น
        $sql_update = " UPDATE ims_product_sale_cockpit 
                        SET DI_REMARK = :DI_REMARK    
                        WHERE DI_KEY = :DI_KEY         
                        AND DI_REF  = :DI_REF
                        AND DI_DATE = :DI_DATE
                        AND DT_DOCCODE = :DT_DOCCODE
                        AND TRD_SEQ = :TRD_SEQ ";

        $query = $conn->prepare($sql_update);

        // Bind เฉพาะตัวแปรที่จำเป็น
        $query->bindParam(':DI_REMARK', $result_sqlsvr["DI_REMARK"], PDO::PARAM_STR);

        // Where conditions
        $query->bindParam(':DI_KEY', $result_sqlsvr["DI_KEY"], PDO::PARAM_STR);
        $query->bindParam(':DI_REF', $result_sqlsvr["DI_REF"], PDO::PARAM_STR);
        $query->bindParam(':DI_DATE', $result_sqlsvr["DI_DATE"], PDO::PARAM_STR);
        $query->bindParam(':DT_DOCCODE', $result_sqlsvr["DT_DOCCODE"], PDO::PARAM_STR);
        $query->bindParam(':TRD_SEQ', $result_sqlsvr["TRD_SEQ"], PDO::PARAM_STR);

        if($query->execute()) {
            echo "UPDATE DI_REMARK SUCCESS: " . $result_sqlsvr["DI_REF"] . "\n\r";
        }
    } else {
        // ถ้าไม่พบข้อมูลเดิม ไม่ทำอะไร (เนื่องจากคุณต้องการ update เท่านั้น ไม่เอา insert)
        echo "SKIP: No record found for " . $result_sqlsvr["DI_REF"] . "\n\r";
    }
}

$conn_sqlsvr = null;
$conn = null;