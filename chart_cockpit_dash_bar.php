<?php

include("config/connect_db.php");

$month_name = "";

//$year = $_POST["year"];
//$month = $_POST["month"];
//$branch = $_POST["branch"]

$year = "2022";
$month = "12";
$branch = "CP-RP";

$sql_month = " SELECT * FROM ims_month where month = '" . $month . "'";
$stmt_month = $conn->prepare($sql_month);
$stmt_month->execute();
$MonthRecords = $stmt_month->fetchAll();
foreach ($MonthRecords as $row) {
    $month_name = $row["month_name"];
}

$sql_branch = " SELECT * FROM ims_branch where branch = '" . $branch . "'";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<body onload="showGraph_Daily();">
<div class="card">
    <input type="hidden" name="month" id="month" value="<?php echo $month; ?>">
    <input type="hidden" name="year" id="year" class="form-control" value="<?php echo $year; ?>">
    <input type="hidden" name="branch" id="branch" value="<?php echo $branch; ?>">
    <input type="hidden" name="branch_name" id="branch_name" class="form-control" value="<?php echo $branch_name; ?>">

    <div class="card-body">

        <div id="chart-container">
            <canvas id="graphCanvas_Daily"></canvas>
        </div>

    </div>
</div>


<script>

    function showGraph_Daily() {
        {

            let month = $("#month").val();
            let year = $("#year").val();
            let branch = $("#branch").val();

            let backgroundColor = '#0a4dd3';
            let borderColor = '#46d5f1';

            let hoverBackgroundColor = '#a2a1a3';
            let hoverBorderColor = '#a2a1a3';

            $.post("engine/chart_data_daily.php", {month: month, year: year, branch: branch}, function (data) {
                console.log(data);
                let date = [];
                let total = [];
                for (let i in data) {
                    date.push(data[i].DI_DATE);
                    total.push(data[i].TRD_G_KEYIN);
                }

                let chartdata = {
                    labels: date,
                    datasets: [{
                        label: 'ยอดขายรายวัน รวม VAT (Daily)',
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        hoverBackgroundColor: hoverBackgroundColor,
                        hoverBorderColor: hoverBorderColor,
                        data: total
                    }]
                };
                let graphTarget = $('#graphCanvas_Daily');
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
