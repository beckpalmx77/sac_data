<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bar chart with data value on the top of each bar</title>
    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script-->
    <script src="../js/jquery-3.6.0.js"></script>
    <script src="../js/chartjs-2.9.0.js"></script>
</head>
<body>
<div class="chart-container" style="position: relative; width:80vw">
    <canvas id="my_Chart"></canvas>
</div>
<script>
    const labels = Utils.months({count: 7});
    const data = {
        labels: labels,
        datasets: [{
            label: 'My First Dataset',
            data: [65, 59, 80, 81, 56, 55, 40],
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    };
</script>
</body>
</html>