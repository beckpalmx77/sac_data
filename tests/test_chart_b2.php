<div>
    <div class="chart-container" style="position: relative; width:80vw">
        <canvas id="myChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            datasets: [{
                label: "My First dataset",
                fillColor: "#FC9775",
                data: [65, 59, 80, 81, 56, 55, 40]
            }, {
                label: "My Second dataset",
                fillColor: "#5A69A6",
                data: [28, 48, 40, 19, 86, 27, 90]
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>



