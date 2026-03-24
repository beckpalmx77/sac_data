<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../config/connect_sqlserver.php');
include("../config/connect_db.php");
date_default_timezone_set('Asia/Bangkok');

// ตั้งชื่อไฟล์
$filename = "Customer_History_By_Car-" . date('Ymd_His') . ".csv";

header('Content-type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=" . $filename);

// รับค่าจาก Form
$customer_name = $_POST["AR_NAME"] ?? "";
$car_no = $_POST["car_no"] ?? "";
$doc_date_start_input = $_POST["doc_date_start"] ?? "";
$doc_date_to_input = $_POST["doc_date_to"] ?? date('d-m-Y');

// แปลงวันที่ (DD-MM-YYYY -> YYYY/MM/DD)
$doc_date_start = substr($doc_date_start_input, 6, 4) . "/" . substr($doc_date_start_input, 3, 2) . "/" . substr($doc_date_start_input, 0, 2);
$doc_date_to = substr($doc_date_to_input, 6, 4) . "/" . substr($doc_date_to_input, 3, 2) . "/" . substr($doc_date_to_input, 0, 2);

// SQL Query: ค้นหาหลักด้วยทะเบียนรถ ถ้าไม่มี则ค้นหาด้วยชื่อลูกค้า
$where_car = $car_no ? "ADDRBOOK.ADDB_SEARCH LIKE '%" . $car_no . "%'" : "";
$where_customer = $customer_name ? "REPLACE(REPLACE(ADDRBOOK.ADDB_COMPANY, '  ', ' '), ' ', '%') LIKE '%" . str_replace(" ", "%", $customer_name) . "%'" : "";

$where_clauses = [];
$where_clauses[] = "ADDRBOOK.ADDB_SEARCH IS NOT NULL AND ADDRBOOK.ADDB_SEARCH <> ''";
if ($car_no) $where_clauses[] = $where_car;
if ($customer_name) $where_clauses[] = $where_customer;

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses) . " AND ";
} else {
    $where_sql = "WHERE ";
}

$sql_string = "
SELECT DISTINCT TOP 5000
    DOCINFO.DI_REF, 
    DOCINFO.DI_DATE,
    DAY(DOCINFO.DI_DATE) AS DI_DAY,
    MONTH(DOCINFO.DI_DATE) AS DI_MONTH,
    YEAR(DOCINFO.DI_DATE) AS DI_YEAR,
    ADDRBOOK.ADDB_COMPANY,
    ADDRBOOK.ADDB_SEARCH,
    SKUMASTER.SKU_CODE,
    SKUMASTER.SKU_NAME,
    TRANSTKD.TRD_QTY,
    TRANSTKD.TRD_Q_FREE,
    TRANSTKD.TRD_U_PRC,
    TRANSTKD.TRD_B_SELL,
    TRANSTKD.TRD_B_VAT,
    TRANSTKD.TRD_B_AMT
FROM DOCINFO 
INNER JOIN TRANSTKH ON DOCINFO.DI_KEY = TRANSTKH.TRH_DI
INNER JOIN TRANSTKD ON TRANSTKH.TRH_KEY = TRANSTKD.TRD_TRH
INNER JOIN SKUMASTER ON TRANSTKD.TRD_SKU = SKUMASTER.SKU_KEY
INNER JOIN ADDRBOOK ON TRANSTKH.TRH_SHIP_ADDB = ADDRBOOK.ADDB_KEY 
" . $where_sql . " DOCINFO.DI_DATE BETWEEN '" . $doc_date_start . "' AND '" . $doc_date_to . "'
ORDER BY DOCINFO.DI_DATE DESC, ADDRBOOK.ADDB_SEARCH ASC";

// ==========================================
// ส่วนการเขียนไฟล์ Text Log (SQL Debug)
// ==========================================

/*
$log_filename = "logs/sql_log_" . date('Ymd') . ".txt"; // ตั้งชื่อไฟล์ log ตามวันที่
if (!is_dir('logs')) { mkdir('logs', 0777, true); } // สร้างโฟลเดอร์ logs ถ้ายังไม่มี

$log_content = "--- Log Entry: " . date('Y-m-d H:i:s') . " ---\n";
$log_content .= "Input Customer: " . $customer_name . "\n";
$log_content .= "Input Car No: " . $car_no . "\n";
$log_content .= "Date Range: " . $doc_date_start . " to " . $doc_date_to . "\n";
$log_content .= "Full SQL:\n" . $sql_string . "\n";
$log_content .= "-------------------------------------------\n\n";
*/

//file_put_contents($log_filename, $log_content, FILE_APPEND); // บันทึกต่อท้ายไฟล์เดิม
// ==========================================

// สร้างหัวตาราง CSV (ใช้ Double Quote ครอบเพื่อกันเพี้ยน)
$data = "No.,เลขที่เอกสาร,วันที่,ชื่อลูกค้า,ทะเบียนรถ,รหัสสินค้า,รายการ,จำนวน,ราคา/หน่วย,ฐานภาษี,ภาษี,ยอดสุทธิ\n";

$statement_sqlsvr = $conn_sqlsvr->query($sql_string);
$line = 0;

if ($statement_sqlsvr) {
    while ($row = $statement_sqlsvr->fetch(PDO::FETCH_ASSOC)) {
        $line++;
        $total_qty = $row['TRD_QTY'] + $row['TRD_Q_FREE'];

        $data .= $line . ",";
        $data .= '"' . $row['DI_REF'] . '",';
        $data .= '"' . $row['DI_DAY'] . "/" . $row['DI_MONTH'] . "/" . $row['DI_YEAR'] . '",';
        $data .= '"' . str_replace('"', '""', $row['ADDB_COMPANY']) . '",';
        $data .= '"' . str_replace('"', '""', $row['ADDB_SEARCH']) . '",'; // แสดงทะเบียนรถแยกคัน
        $data .= '"' . $row['SKU_CODE'] . '",';
        $data .= '"' . str_replace('"', '""', $row['SKU_NAME']) . '",';
        $data .= $total_qty . ",";
        $data .= number_format($row['TRD_U_PRC'], 2, '.', '') . ",";
        $data .= number_format($row['TRD_B_SELL'], 2, '.', '') . ",";
        $data .= number_format($row['TRD_B_VAT'], 2, '.', '') . ",";
        $data .= number_format($row['TRD_B_AMT'], 2, '.', '') . "\n";
    }
}

// ใส่ BOM ให้ Excel เปิดภาษาไทยได้ไม่เป็นต่างดาว
echo "\xEF\xBB\xBF";
echo $data;
exit;