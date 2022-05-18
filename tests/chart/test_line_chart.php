<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

    $(document).ready(function () {
        alert("66");
        $.ajax({
            type: "POST",
            url: 'get_data.php',
            dataType: "json",
            data: formData,
            success: function (response) {
                alert(response);
            },
            error: function (response) {
                alertify.error("error : " + response);
            }
        });
    });

</script>


<script>


        let data1 = [0, 10, 5, 2, 20, 30, 45];
        let data2 = [5, 15, 25, 32, 25, 35, 55];
        let data3 = [5, 25, 45, 52, 45, 35, 55];
        let data4 = [5, 35, 15, 22, 35, 15, 35];

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
                label: 'My 1 dataset',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: data1,
            },
                {
                    label: 'My 2 dataset',
                    backgroundColor: 'rgb(243,87,4)',
                    borderColor: 'rgb(248,117,85)',
                    data: data2,
                },
                {
                    label: 'My 3 dataset',
                    backgroundColor: 'rgb(16,241,46)',
                    borderColor: 'rgb(135,245,88)',
                    data: data3,
                },
                {
                    label: 'My 4 dataset',
                    backgroundColor: 'rgb(6,107,215)',
                    borderColor: 'rgb(88,141,245)',
                    data: data4,
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