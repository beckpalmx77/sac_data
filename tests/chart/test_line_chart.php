<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    const labels = [
        'มกราคม',
        'กุมภาพันธ์',
        'มีนาคม',
        'เมษายน',
        'พฤษภาคม',
        'มิถุนายน',
        'กรกฎาคม',
        'สิงหาคม',
        'กันยายน',
        'พฤศจิกายน',
        'ธันวาคม',
    ];

    const data = {
        labels: labels,
        datasets: [{
            label: 'My First dataset',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [0, 10, 5, 2, 20, 30, 45],
        },
            {
                label: 'My First dataset',
                backgroundColor: 'rgb(243,87,4)',
                borderColor: 'rgb(248,117,85)',
                data: [10, 12, 15, 12, 20, 25, 35],
            },
            {
                label: 'My First dataset',
                backgroundColor: 'rgb(16,241,46)',
                borderColor: 'rgb(135,245,88)',
                data: [0, 0, 15, 18, 23, 45, 25],
            },
            {
                label: 'My First dataset',
                backgroundColor: 'rgb(6,107,215)',
                borderColor: 'rgb(88,141,245)',
                data: [20, 22, 35, 32, 10, 45, 55],
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {}
    };
</script>

<style>

    body {
        width: 1024px;
        margin: 3rem auto;
    }

    #chart-container {
        width: 100%;
        height: auto;
    }
</style>

<div>
    <canvas id="myChart"></canvas>
</div>

<script>
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
</script>