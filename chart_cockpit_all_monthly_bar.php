<?php

include("config/connect_db.php");

//$month = $_POST["month"];
//$year = $_POST["year"];

$month = "4";
$year = "2022";

$month_name = "";

$sql_month = " SELECT * FROM ims_month where month = '" . $month . "'";
$stmt_month = $conn->prepare($sql_month);
$stmt_month->execute();
$MonthRecords = $stmt_month->fetchAll();
foreach ($MonthRecords as $row) {
    $month_name = $row["month_name"];
}

//$myfile = fopen("param_post.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $month . "| month_name " . $month_name . "| branch = " . $_POST["branch"] . "| Branch Name = "
//    . $branch_name . " | " . $sql_month . " | " . $sql_branch);
//fclose($myfile);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta date="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <script src="js/jquery-3.6.0.js"></script>
    <!--script src="js/chartjs-2.9.0.js"></script-->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="fontawesome/css/font-awesome.css">

    <link href='vendor/calendar/main.css' rel='stylesheet'/>
    <script src='vendor/calendar/main.js'></script>
    <script src='vendor/calendar/locales/th.js'></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>


    <title>สงวนออโต้คาร์</title>

</head>

<body onload="showGraph_Data_Monthly(1);showGraph_Data_Monthly(2);showGraph_Data_Monthly(3);">

<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fa fa-bar-chart" aria-hidden="true"></i>ยอดขายเปรียบเทียบ
        <?php echo "เดือน" . $month_name . " ปี " . $year; ?>
    </div>
    <input type="hidden" name="month" id="month" value="<?php echo $month; ?>">
    <input type="hidden" name="year" id="year" class="form-control" value="<?php echo $year; ?>">
    <div class="card-body">

        <div class="card-body">
            <table id="example" class="display table table-striped table-bordered"
                   cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>สาขา</th>
                    <th>ยอดขาย ยาง</th>
                    <th>ยอดขาย อะไหล่</th>
                    <th>ยอด ค่าแรง-ค่าบริการ</th>
                    <th>ยอดรวม</th>
                </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                <?php
                $date = date("d/m/Y");
                $total = 0;
                $sql_daily = " SELECT *
 FROM ims_report_product_sale_summary 
 WHERE DI_YEAR = '" . $year . "' 
 AND DI_MONTH = '" . $month . "'
 ORDER BY DI_MONTH" ;

                $statement_daily = $conn->query($sql_daily);
                $results_daily = $statement_daily->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results_daily

                as $row_daily) { ?>

                <tr>
                    <td><?php echo htmlentities($row_daily['BRANCH']); ?></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['tires_total_amt'], 2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['part_total_amt'], 2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['svr_total_amt'], 2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['total_amt'], 2)); ?></p></td>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



            <table id="example" class="display table table-striped table-bordered"
                   cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>สาขา</th>
                    <th>BS</th>
                    <th>BS</th>
                    <th>FS</th>
                    <th>FS</th>
                    <th>DL</th>
                    <th>DL</th>
                    <th>LLIT</th>
                    <th>LLIT</th>
                    <th>DS</th>
                    <th>DS</th>
                    <th>DT</th>
                    <th>DT</th>
                    <th>ML</th>
                    <th>ML</th>
                    <th>PL</th>
                    <th>PL</th>
                    <th>AT</th>
                    <th>AT</th>
                    <th>CT</th>
                    <th>CT</th>
                    <th>GY</th>
                    <th>GY</th>
                    <th>LE</th>
                    <th>LE</th>
                    <th>YK</th>
                    <th>YK</th>
                </tr>
                <tr>
                    <th></th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                    <th>(เส้น)</th>
                    <th>(บาท)</th>
                </tr>
                </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                <?php
                $date = date("d/m/Y");
                $total = 0;
                $sql_daily = " 
SELECT
BRANCH,
SUM(IF(BRN_CODE='BS',TRD_QTY,0)) AS BS_QTY,
SUM(IF(BRN_CODE='BS',TRD_G_KEYIN,0)) AS BS_AMT,
SUM(IF(BRN_CODE='FS',TRD_QTY,0)) AS FS_QTY,
SUM(IF(BRN_CODE='FS',TRD_G_KEYIN,0)) AS FS_AMT,
SUM(IF(BRN_CODE='DL',TRD_QTY,0)) AS DL_QTY,
SUM(IF(BRN_CODE='DL',TRD_G_KEYIN,0)) AS DL_AMT,
SUM(IF(BRN_CODE='LLIT',TRD_QTY,0)) AS LLIT_QTY,
SUM(IF(BRN_CODE='LLIT',TRD_G_KEYIN,0)) AS LLIT_AMT,
SUM(IF(BRN_CODE='DS',TRD_QTY,0)) AS DS_QTY,
SUM(IF(BRN_CODE='DS',TRD_G_KEYIN,0)) AS DS_AMT,
SUM(IF(BRN_CODE='DT',TRD_QTY,0)) AS DT_QTY,
SUM(IF(BRN_CODE='DT',TRD_G_KEYIN,0)) AS DT_AMT,
SUM(IF(BRN_CODE='ML',TRD_QTY,0)) AS ML_QTY,
SUM(IF(BRN_CODE='ML',TRD_G_KEYIN,0)) AS ML_AMT,
SUM(IF(BRN_CODE='PL',TRD_QTY,0)) AS PL_QTY,
SUM(IF(BRN_CODE='PL',TRD_G_KEYIN,0)) AS PL_AMT,
SUM(IF(BRN_CODE='AT',TRD_QTY,0)) AS AT_QTY,
SUM(IF(BRN_CODE='AT',TRD_G_KEYIN,0)) AS AT_AMT,
SUM(IF(BRN_CODE='CT',TRD_QTY,0)) AS CT_QTY,
SUM(IF(BRN_CODE='CT',TRD_G_KEYIN,0)) AS CT_AMT,
SUM(IF(BRN_CODE='GY',TRD_QTY,0)) AS GY_QTY,
SUM(IF(BRN_CODE='GY',TRD_G_KEYIN,0)) AS GY_AMT,
SUM(IF(BRN_CODE='LE',TRD_QTY,0)) AS LE_QTY,
SUM(IF(BRN_CODE='LE',TRD_G_KEYIN,0)) AS LE_AMT,
SUM(IF(BRN_CODE='YK',TRD_QTY,0)) AS YK_QTY,
SUM(IF(BRN_CODE='YK',TRD_G_KEYIN,0)) AS YK_AMT                
 FROM ims_product_sale_cockpit 
 WHERE DI_YEAR = '" . $year . "' 
 AND PGROUP like '%P1'
 GROUP BY BRANCH 
 ORDER BY DI_MONTH" ;

                $statement_daily = $conn->query($sql_daily);
                $results_daily = $statement_daily->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results_daily

                as $row_daily) { ?>

                <tr>
                    <td><?php echo htmlentities($row_daily['BRANCH']); ?></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['BS_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['BS_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['FS_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['FS_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['DL_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['DL_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['LLIT_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['LLIT_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['DS_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['DS_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['DT_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['DT_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['ML_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['ML_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['PL_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['PL_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['AT_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['AT_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['CT_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['CT_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['GY_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['GY_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['LE_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['LE_AMT'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['YK_QTY'] ,2)); ?></p></td>
                    <td><p class="number"><?php echo htmlentities(number_format($row_daily['YK_AMT'] ,2)); ?></p></td>
                    <?php } ?>

                </tbody>
            </table>


<div class="card">
    <div class="card-header bg-success text-white">
    </div>
    <input type="hidden" name="month" id="month" value="<?php echo $month; ?>">
    <input type="hidden" name="year" id="year" value="<?php echo $year; ?>">
    <div class="card-body">
        <div id="chart-container">
            <canvas id="graphCanvas_Part_Monthly"></canvas>
        </div>
    </div>

    <div class="card-body">
        <table id="example" class="display table table-striped table-bordered"
               cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>อะไหล่</th>
                <th>ยอดขาย</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>อะไหล่</th>
                <th>ยอดขาย</th>
            </tr>
            </tfoot>
            <tbody>
            <?php
            $total = 0;
            $total_sale = 0;
            $sql_brand = " SELECT SKU_CAT,ICCAT_NAME,sum(CAST(TRD_QTY AS DECIMAL(10,2))) as  TRD_QTY,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as TRD_G_KEYIN 
 FROM ims_product_sale_cockpit
 WHERE PGROUP = 'P2'
 AND DI_YEAR = '" . $year . "'
 AND DI_MONTH = '" . $month . "'
 GROUP BY SKU_CAT,ICCAT_NAME
 ORDER BY SKU_CAT ";

            $statement_brand = $conn->query($sql_brand);
            $results_brand = $statement_brand->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results_brand

            as $row_brand) { ?>

            <tr>
                <td><?php echo htmlentities($row_brand['ICCAT_NAME']); ?></td>
                <td><p class="number"><?php echo htmlentities(number_format($row_brand['TRD_G_KEYIN'], 2)); ?></p></td>

                <?php } ?>

            </tbody>
        </table>
    </div>

</div>


</body>
</html>

