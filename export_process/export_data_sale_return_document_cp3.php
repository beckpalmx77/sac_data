<?php
date_default_timezone_set('Asia/Bangkok');

// --- ส่วนที่ 1: ฟังก์ชันสำหรับการเขียน Log ---
function write_sql_log($sql, $params) {
    $full_sql = $sql;
    // แทนที่ Placeholder ด้วยค่าจริง เพื่อให้ Copy ไปรันใน SQL Management Studio ได้ง่าย
    foreach ($params as $key => $value) {
        $clean_value = is_numeric($value) ? $value : "'" . str_replace("'", "''", $value) . "'";
        $full_sql = str_replace($key, $clean_value, $full_sql);
    }

    $log_dir = "../logs";
    if (!filter_var($log_dir, FILTER_VALIDATE_URL) && !file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $log_file = $log_dir . "/query_log_" . date('Y-m-d') . ".log";
    $timestamp = date('Y-m-d H:i:s');
    $log_content = "[$timestamp] EXECUTED QUERY:\n$full_sql\n" . str_repeat("-", 50) . "\n";

    file_put_contents($log_file, $log_content, FILE_APPEND);
}

// --- ส่วนที่ 2: ตั้งค่า Header สำหรับ Download CSV ---
$filename = "Data_Customer-Service-" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=$filename");

// เชื่อมต่อฐานข้อมูลและดึงไฟล์ตั้งค่า
include('../config/connect_sqlserver.php');
include('../cond_file/query_customer_history_service.php');
include('../util/month_util.php');

// รับค่าจาก Form
$customer_name = $_POST["customer_name"] ?? '';
$car_no = $_POST["car_no"] ?? '';
$date_option = $_POST['date_option'] ?? '';
$doc_date_start = $_POST['doc_date_start'] ?? '';
$doc_date_to = $_POST['doc_date_to'] ?? '';

$where_date = "";
$where_params = [];

if ($date_option === 'range') {
    if (!empty($doc_date_start)) {
        $doc_date_start = substr($doc_date_start, 6, 4) . "/" . substr($doc_date_start, 3, 2) . "/" . substr($doc_date_start, 0, 2);
    }
    if (!empty($doc_date_to)) {
        $doc_date_to = substr($doc_date_to, 6, 4) . "/" . substr($doc_date_to, 3, 2) . "/" . substr($doc_date_to, 0, 2);
    }
    if (!empty($doc_date_start) && !empty($doc_date_to)) {
        $where_date = " AND DI_DATE BETWEEN :doc_date_start AND :doc_date_to ";
        $where_params[':doc_date_start'] = $doc_date_start;
        $where_params[':doc_date_to'] = $doc_date_to;
    }
}

// สร้างเงื่อนไข SQL เสริม
$sql_and = "";
if (!empty($customer_name)) {
    $sql_and .= " AND REPLACE(REPLACE(ADDRBOOK.ADDB_COMPANY, '  ', ' '), ' ', '%') LIKE :customer_name ";
    $where_params[':customer_name'] = '%' . str_replace(' ', '%', $customer_name) . '%';
}
if (!empty($car_no)) {
    $sql_and .= " AND ADDRBOOK.ADDB_SEARCH LIKE :car_no ";
    $where_params[':car_no'] = '%' . $car_no . '%';
}

// รวม Query ทั้งหมด
$String_Sql = $str_sql_comm . $sql_and . $where_date . $str_sql_order;

// บันทึก Log ก่อน Execute
// write_sql_log($String_Sql, $where_params);

$query = $conn_sqlsvr->prepare($String_Sql);

// Bind parameters
foreach ($where_params as $param_name => $param_value) {
    $query->bindValue($param_name, $param_value, PDO::PARAM_STR);
}

$query->execute();

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($query->rowCount() == 0) {
    die("❌ ไม่พบข้อมูลในฐานข้อมูล");
}

// ส่วนหัว CSV
$data = "ลำดับที่,วัน,เดือน,ปี,เลขที่เอกสาร,รหัสลูกค้า,ชื่อลูกค้า,หมายเลขโทรศัพท์,ทะเบียนรถ,ยี่ห้อรถ/รุ่น,เลขไมล์,รหัสสินค้า,ชื่อสินค้า,จำนวน,ราคาต่อหน่วย,จำนวนเงิน\n";

$line = 0;
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $line++;
    $month_name = $month_arr[$row['DI_MONTH']] ?? '';
    $year = $row['DI_YEAR'] ?? '';
    $TRD_QTY = ($row['TRD_Q_FREE'] > 0) ? ($row['TRD_QTY'] + $row['TRD_Q_FREE']) : ($row['TRD_QTY'] ?? 0);

    $addb_phone = $row['ADDB_PHONE_MAIN'] ?? "";
    $AR_CODE = $row['AR_CODE_MAIN'] ?? "";

    // เตรียมข้อมูล String ป้องกันเครื่องหมาย Comma ใน CSV
    $ADDB_COMPANY = str_replace(",", " ", $row['ADDB_COMPANY'] ?? "");
    $DI_REF = str_replace(",", " ", $row['DI_REF'] ?? '');
    $SKU_CODE = str_replace(",", " ", $row['SKU_CODE'] ?? '');
    $SKU_NAME = str_replace(",", " ", $row['SKU_NAME'] ?? '');

    $data .= "$line,{$row['DI_DAY']},$month_name,$year,$DI_REF,$AR_CODE,$ADDB_COMPANY,$addb_phone,{$row['ADDB_BRANCH']},{$row['ADDB_ADDB_1']} {$row['ADDB_ADDB_2']},{$row['ADDB_ADDB_3']},$SKU_CODE,$SKU_NAME,$TRD_QTY,{$row['TRD_U_PRC']},{$row['TRD_B_AMT']}\n";
}

// พิมพ์ BOM เพื่อให้ Excel อ่านภาษาไทยออก และพ่นข้อมูล CSV
echo "\xEF\xBB\xBF";
echo $data;
exit();