<?php
// Require composer autoload
require_once '../vendor/autoload.php';

include '../includes/SetFont.php';

ob_start(); // Start get HTML code
?>

<!DOCTYPE html>
<html>
<head>
    <title>PDF</title>
    <link href="https://fonts.googleapis.com/css?family=Sarabun&display=swap" rel="stylesheet">

    <style>
        .container{
            font-family: "sarabun";
            font-size: 12pt;
        }
        p{
            text-align: justify;
        }
        h1{
            text-align: center;
        }
    </style>

    <style>
        body {
            font-family: "sarabun";
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body>

<div class="container" style="width: 100%">

<h1>ตัวอย่างในการเก็บโค้ด HTML มาเป็น PDF</h1>
<table>
    <tr>
        <th>ชื่อ</th>
        <th>ที่อยู่</th>
        <th>ประเทศ</th>
    </tr>
    <tr>
        <td>น้องไก่ คนงาม</td>
        <td>ลำพูน</td>
        <td>ไทย</td>
    </tr>
    <tr>
        <td>นายรักเรียน</td>
        <td>Francisco Chang</td>
        <td>Mexico</td>
    </tr>
    <tr>
        <td>นายรักดี</td>
        <td>Roland Mendel</td>
        <td>Austria</td>
    </tr>
</table>

</div>

</body>
</html>

<?php

$mpdf = new \Mpdf\Mpdf();
$html = ob_get_contents(); // ทำการเก็บค่า HTML จากคำสั่ง ob_start()
$mpdf->WriteHTML($html); // ทำการสร้าง PDF ไฟล์
$mpdf->Output("MyPDF.pdf"); // ให้ทำการบันทึกโค้ด HTML เป็น PDF โดยบันทึกเป็นไฟล์ชื่อ MyPDF.pdf
ob_end_flush();

?>

ดาวโหลดรายงานในรูปแบบ PDF <a href="MyPDF.pdf" target="_blank">คลิกที่นี้</a>
