<?php

include("config/connect_db.php");

$month_name = "";

$sql_month = " SELECT * FROM ims_month where month = '" . $_POST["month"] . "'";
$stmt_month = $conn->prepare($sql_month);
$stmt_month->execute();
$MonthRecords = $stmt_month->fetchAll();
foreach ($MonthRecords as $row) {
    $month_name = $row["month_name"];
}

$sql_branch = " SELECT * FROM ims_branch where branch = '" . $_POST["branch"] . "'";
$stmt_branch = $conn->prepare($sql_branch);
$stmt_branch->execute();
$BranchRecords = $stmt_branch->fetchAll();
foreach ($BranchRecords as $rows) {
    $branch_name = $rows["branch_name"];
}

//$myfile = fopen("param_post.txt", "w") or die("Unable to open file!");
//fwrite($myfile, $_POST["month"] . "| month_name " . $month_name . "| branch = " . $_POST["branch"] . "| Branch Name = "
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
    <script src="js/chartjs-2.9.0.js"></script>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="fontawesome/css/font-awesome.css">
    <title>สงวนออโต้คาร์</title>
    <style>

        body {
            width: 620px;
            margin: 3rem auto;
        }

        #chart-container {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body onload="showGraph_Data_Monthly(1);showGraph_Data_Monthly(2);showGraph_Data_Monthly(3);">
<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fa fa-bar-chart" aria-hidden="true"></i> แสดง Chart ยอดขายเปรียบเทียบ
        <?php echo $branch_name . " เดือน " . $month_name . " ปี " . $_POST["year"]; ?>
    </div>
    <input type="hidden" name="month" id="month" value="<?php echo $_POST["month"]; ?>">
    <!--input type="text" name="month_name" id="month_name" class="form-control" value="<?php echo $month_name; ?>"-->
    <input type="hidden" name="year" id="year" class="form-control" value="<?php echo $_POST["year"]; ?>">

    <input type="hidden" name="branch" id="branch" value="<?php echo $_POST["branch"]; ?>">
    <input type="hidden" name="branch_name" id="branch_name" class="form-control" value="<?php echo $branch_name; ?>">

    <div class="card-body">

        <div id="chart-container">
            <canvas id="graphCanvas_P1_Monthly"></canvas>
        </div>

        <div id="chart-container">
            <canvas id="graphCanvas_P2_Monthly"></canvas>
        </div>

        <div id="chart-container">
            <canvas id="graphCanvas_P3_Monthly"></canvas>
        </div>

    </div>
</div>


<script>
    function showGraph_Data_Monthly(p_group) {
        {

            let month = $("#month").val();
            let year = $("#year").val();
            let branch = $("#branch").val();

            let backgroundColor = '#bd58fa';
            let borderColor = '#46d5f1';
            let hoverBackgroundColor = '#a2a1a3';
            let hoverBorderColor = '#a2a1a3';

            let graphTarget = '';
            let graphlabel = '';

            //alert(p_group);

            if (p_group===1) {
                PGROUP = 'P1';
                graphTarget = $('#graphCanvas_P1_Monthly');
                graphlabel = 'ยอดขาย ยาง รายเดือน รวม VAT (Monthly)';
                backgroundColor = '#bd58fa';
                borderColor = '#46d5f1';
                hoverBackgroundColor = '#a2a1a3';
                hoverBorderColor = '#a2a1a3';
                //alert("L1 = " + p_group);
            } else if (p_group===2) {
                PGROUP = 'P2';
                graphTarget = $('#graphCanvas_P2_Monthly');
                graphlabel = 'ยอดขาย อะไหล่ รายเดือน รวม VAT (Monthly)';
                backgroundColor = '#07b65c';
                borderColor = '#b0fcc1';
                hoverBackgroundColor = '#a2a1a3';
                hoverBorderColor = '#a2a1a3';
            } else if (p_group===3) {
                PGROUP = 'P3';
                graphTarget = $('#graphCanvas_P3_Monthly');
                graphlabel = 'ยอดค่าแรง-ค่าบริการ รายเดือน รวม VAT (Monthly)';
                backgroundColor = '#013b82';
                borderColor = '#80caf3';
                hoverBackgroundColor = '#a2a1a3';
                hoverBorderColor = '#a2a1a3';
            }

            //alert(graphTarget);

            $.post("engine/chart_data_by_pgroup_monthly.php", {month: month, year: year, branch: branch ,PGROUP: PGROUP}, function (data) {
                console.log(data);
                let month = [];
                let total = [];
                for (let i in data) {
                    month.push(data[i].DI_MONTH_NAME);
                    total.push(data[i].TRD_G_KEYIN);
                }

                let chartdata = {
                    labels: month,
                    datasets: [{
                        label: graphlabel,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        hoverBackgroundColor: hoverBackgroundColor,
                        hoverBorderColor: hoverBorderColor,
                        data: total
                    }]
                };

                let barGraph = new Chart(graphTarget, {
                    type: 'bar',
                    data: chartdata
                })
            })
        }
    }

</script>

</body>
</html>
