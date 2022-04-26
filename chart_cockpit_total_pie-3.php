<?php

include("config/connect_db.php");

//$doc_date = substr($_POST['doc_date'], 6, 4) . "/" . substr($_POST['doc_date'], 3, 2) . "/" . substr($_POST['doc_date'], 0, 2);
$month = $_POST['month'];
$year = $_POST['year'];

$sql_branch = " SELECT * FROM ims_branch where branch = '" . $_POST["branch"] . "'";
$stmt_branch = $conn->prepare($sql_branch);
$stmt_branch->execute();
$BranchRecords = $stmt_branch->fetchAll();
foreach ($BranchRecords as $rows) {
    $branch_name = $rows["branch_name"];
}


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
            width: 800px;
            margin: 3rem auto;
        }

        #chart-container {
            width: 100%;
            height: auto;
        }
    </style>

    <style>
        p.number {
            text-align-last: right;
        }
    </style>
</head>

<body onload="showGraph_Monthly()">
<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fa fa-bar-chart" aria-hidden="true"></i> กราฟแสดงยอดขาย เดือน <?php echo $_POST["month"]; ?>
        <?php echo $branch_name; ?>
    </div>
    <input type="hidden" name="month" id="month" value="<?php echo $month; ?>">
    <input type="hidden" name="year" id="year" value="<?php echo $year; ?>">
    <input type="hidden" name="branch" id="branch" value="<?php echo $_POST["branch"]; ?>">
    <input type="hidden" name="branch_name" id="branch_name" class="form-control" value="<?php echo $branch_name; ?>">
    <div class="card-body">
        <div id="chart-container">
            <canvas id="graphCanvas_Monthly"></canvas>
        </div>

    </div>
</div>


<?php
include("display_data_cockpit_detail_grp_monthly.php");
?>

<!--?php
include("display_data_cockpit_detail.php");
?-->


<script>

    function showGraph_Monthly() {
        {

            let month = $("#month").val();
            let year = $("#year").val();
            let branch = $("#branch").val();

            let backgroundColor = '#0a4dd3';
            let borderColor = '#46d5f1';

            let hoverBackgroundColor = '#a2a1a3';
            let hoverBorderColor = '#a2a1a3';

            let barColors = [
                "#0a4dd3",
                "#c21bf8",
                "#f3661a",
                "#b91d47",
                "#00aba9",
                "#f81b61",
                "#fcae13"

            ];

            $.post("engine/chart_data_pie_monthly.php", {month: month ,year: year ,branch: branch }, function (data) {
                console.log(data);
                let label = [];
                let total = [];
                for (let i in data) {
                    label.push(data[i].pgroup_name);
                    total.push(data[i].TRD_G_KEYIN);
                    //alert(label);
                }

                new Chart("graphCanvas_Monthly", {
                    type: "pie",
                    data: {
                        labels: label,
                        datasets: [{
                            backgroundColor: barColors,
                            data: total
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: ""
                        }
                    }
                });

            })


        }
    }

</script>



</body>
</html>
