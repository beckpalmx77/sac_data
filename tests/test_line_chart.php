<!DOCTYPE html>
<html>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<body>
<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<script>
    let label1 = 'XD';
    let label2 = 'XC';
    let label3 = 'XE';
    var xValues = [100,200,300,400,500,600,700,800,900,1000];

    new Chart("myChart", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: label1,
                data: [860,1140,1060,1060,1070,1110,1330,2210,7830,2478],
                borderColor: "red",
                fill: false
            }, {
                label: label2,
                data: [1600,1700,1700,1900,2000,2700,4000,5000,6000,7000],
                borderColor: "green",
                fill: false
            }, {
                label: label3,
                data: [300,700,2000,5000,6000,4000,2000,1000,200,100],
                borderColor: "blue",
                fill: false
            }]
        },
        options: {
            legend: {display: true}
        }
    });
</script>

