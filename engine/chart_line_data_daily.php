<?php
header('Content-Type: application/json');

include("config/connect_db.php");

$month = $_POST["month"];
$year = $_POST["year"];
$branch = $_POST["branch"];

//$myfile = fopen("param.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $month  . "| Year = " . $year . "| Branch" . $branch );
//fclose($myfile);

$sql_get = "
 SELECT BRANCH,DAY(STR_TO_DATE(DI_DATE,'%d/%m/%Y')) as DI_DATE,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as  TRD_G_KEYIN
 FROM ims_product_sale_cockpit 
 WHERE DI_YEAR = '" . $year . "'   AND DI_MONTH = '" . $month . "'
 and BRANCH like '%" . $branch . "'
 AND ICCAT_CODE <> '6SAC08'  AND (DT_DOCCODE <> 'IS' OR DT_DOCCODE <> 'IIS' OR DT_DOCCODE <> 'IC')
 GROUP BY  BRANCH,DI_DATE
 ORDER BY STR_TO_DATE(DI_DATE,'%d/%m/%Y')
";

$return_arr = array();

$statement = $conn->query($sql_get);
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {
  $return_arr[] = array("DI_DATE" => $result['DI_DATE'],
      "TRD_G_KEYIN" => $result['TRD_G_KEYIN']);
}

//$myfile = fopen("qry_file1.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $sql_get);
//fclose($myfile);

echo json_encode($return_arr);

