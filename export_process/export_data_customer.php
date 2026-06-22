<?php
date_default_timezone_set('Asia/Bangkok');

$com_code = $_POST['com_code'] ?? 'SAC';
$com_code_param = $com_code . '%';

$filename = "Data_Customer_" . $com_code . "-" . date('Ymd_His') . ".csv";

@header('Content-type: text/csv; charset=UTF-8');
@header('Content-Encoding: UTF-8');
@header("Content-Disposition: attachment; filename=" . $filename);

include('../config/connect_sqlserver.php');

$String_Sql = "
SELECT 
    ARFILE.AR_CODE,
    ARFILE.AR_NAME,
    ADDB_BILL.ADDB_ADDB_1 AS ADDRESS,
    ADDB_BILL.ADDB_ADDB_2 AS TUMBOL,
    ADDB_BILL.ADDB_ADDB_3 AS AMPHURE,
    ADDB_BILL.ADDB_PROVINCE AS PROVINCE,
    ADDB_BILL.ADDB_POST AS ZIPCODE,
    ADDB_VEH.ADDB_SEARCH AS VEHICLE_REG_NO,
    ADDB_VEH.ADDB_ADDB_1 AS VEHICLE_BRAND,
    ADDB_VEH.ADDB_ADDB_2 AS VEHICLE_MODEL
FROM ARFILE
JOIN ARCAT ON ARCAT.ARCAT_KEY = ARFILE.AR_ARCAT
JOIN ARSUMMARY ON ARSUMMARY.ARS_AR = ARFILE.AR_KEY
JOIN ACCOUNTCHART ON ARFILE.AR_AC = ACCOUNTCHART.AC_KEY
JOIN DEPTTAB ON ARFILE.AR_DEPT = DEPTTAB.DEPT_KEY
JOIN ARGL ON ARFILE.AR_ARGL = ARGL_KEY
JOIN ARCONDITION ON ARFILE.AR_KEY = ARCONDITION.ARCD_AR
-- ดึงข้อมูลที่อยู่หลัก (สำหรับที่อยู่ ตำบล อำเภอ จังหวัด)
LEFT JOIN ARADDRESS ARA_BILL ON ARFILE.AR_KEY = ARA_BILL.ARA_AR AND ARA_BILL.ARA_DEFAULT = 'Y'
LEFT JOIN ADDRBOOK ADDB_BILL ON ARA_BILL.ARA_ADDB = ADDB_BILL.ADDB_KEY
-- ดึงข้อมูลรถยนต์ (สำหรับเลขทะเบียนรถ ยี่ห้อ รุ่น)
LEFT JOIN ARADDRESS ARA_VEH ON ARFILE.AR_KEY = ARA_VEH.ARA_AR AND ARA_VEH.ARA_DEFAULT = 'N'
LEFT JOIN ADDRBOOK ADDB_VEH ON ARA_VEH.ARA_ADDB = ADDB_VEH.ADDB_KEY AND ADDB_VEH.ADDB_SEARCH IS NOT NULL AND ADDB_VEH.ADDB_SEARCH <> ''
WHERE ARFILE.AR_KEY >= 0
  AND (ARCONDITION.ARCD_DEFAULT='Y')
  AND ARFILE.AR_ENABLE='Y'
  AND ARFILE.AR_CODE LIKE :com_code
ORDER BY ARCAT.ARCAT_CODE ASC, ARFILE.AR_CODE ASC
";

$data = "ลำดับ,รหัสลูกค้า,ชื่อลูกค้า,ที่อยู่,ตำบล/แขวง,อำเภอ/เขต,จังหวัด,รหัสไปรษณีย์,ทะเบียนรถ,ยี่ห้อรถ,รุ่นรถ\n";

$query = $conn_sqlsvr->prepare($String_Sql);
$query->bindParam(':com_code', $com_code_param, PDO::PARAM_STR);
$query->execute();

$loop = 0;

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $loop++;

    $data .= $loop . ",";
    $data .= '"' . str_replace('"', '""', $row['AR_CODE'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['AR_NAME'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['ADDRESS'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['TUMBOL'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['AMPHURE'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['PROVINCE'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['ZIPCODE'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['VEHICLE_REG_NO'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['VEHICLE_BRAND'] ?? '') . '",';
    $data .= '"' . str_replace('"', '""', $row['VEHICLE_MODEL'] ?? '') . "\"\n";
}

$data = iconv("utf-8", "windows-874//IGNORE", $data);
echo $data;

exit();