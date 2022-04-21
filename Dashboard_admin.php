<?php
include('includes/Header.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {

    include("config/connect_db.php");

    ?>

    <!DOCTYPE html>
    <html lang="th">
    <body id="page-top" onload="showGraph_Tires_Brand();">
    <div id="wrapper">
        <?php
        include('includes/Side-Bar.php');
        ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php
                include('includes/Top-Bar.php');
                ?>
                <div class="container-fluid" id="container-wrapper">
                    <div class="row mb-3">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">All Order
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><p class="text-success"
                                                                                                   id="Text1"></p></div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <span class="text-success mr-2"><i
                                                            class="fa fa-arrow-up"></i></span>
                                                <span>Since last month</span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Earnings (Annual) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Product
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><p class="text-success"
                                                                                                   id="Text2"></p></div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <span class="text-success mr-2"><i
                                                            class="fas fa-arrow-up"></i> 12%</span>
                                                <span>Since last years</span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- New User Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Customer
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><p class="text-success"
                                                                                                   id="Text3"></p></div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <span class="text-success mr-2"><i
                                                            class="fas fa-arrow-up"></i> 20.4%</span>
                                                <span>Since last month</span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Supplier
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><p class="text-success"
                                                                                                   id="Text4"></p></div>
                                            <div class="mt-2 mb-0 text-muted text-xs">
                                                <!--span class="text-danger mr-2"><i
                                                            class="fas fa-arrow-down"></i> 1.10%</span>
                                                    <span>Since yesterday</span-->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-warehouse fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    สถิติ
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">ปี 2565</h5>
                                    <canvas id="myChart" width="200" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    สถิติ มูลค่าการขายยาง Cockpit แต่ละยี่ห้อ
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">ปี <?php echo date("Y");?></h5>
                                    <canvas id="myChart2" width="200" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <?php
    include('includes/Modal-Logout.php');
    include('includes/Footer.php');
    ?>
    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/myadmin.min.js"></script>
    <script src="js/chart.js"></script>

    <link href='vendor/calendar/main.css' rel='stylesheet'/>
    <script src='vendor/calendar/main.js'></script>
    <script src='vendor/calendar/locales/th.js'></script>


    <script>

        $(document).ready(function () {

            GET_DATA("ims_order_master", "1");
            GET_DATA("ims_product", "2");
            GET_DATA("ims_customer", "3");
            GET_DATA("ims_supplier", "4");

            setInterval(function () {
                GET_DATA("ims_order_master", "1");
                GET_DATA("ims_product", "2");
                GET_DATA("ims_customer", "3");
                GET_DATA("ims_supplier", "4");
            }, 3000);
        });

    </script>

    <script>

        function GET_DATA(table_name, idx) {
            let input_text = document.getElementById("Text" + idx);
            let action = "GET_COUNT_RECORDS";
            let formData = {action: action, table_name: table_name};
            $.ajax({
                type: "POST",
                url: 'model/manage_general_data.php',
                data: formData,
                success: function (response) {
                    input_text.innerHTML = response;
                },
                error: function (response) {
                    alertify.error("error : " + response);
                }
            });
        }

    </script>

    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: 'Data1',
                    data: [21, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: [
                        'rgba(238, 34, 200, 1)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderWidth: 1
                }, {
                    label: 'Data2',
                    data: [14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: [
                        'rgba(59, 252,200, 1)',
                    ],
                    borderColor: [
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    borderWidth: 1

                }
                ]
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

    <script>

        function showGraph_Tires_Brand() {
            {

                let barColors = [
                    "#0a4dd3",
                    "#c21bf8",
                    "#f3661a",
                    "#f81b61",
                    "#12f361",
                    "#1da5f2",
                    "#af43f5",
                    "#0e0b71",
                    "#e9e207",
                    "#07e9d8",
                    "#b91d47",
                    "#00aba9",
                    "#fa6ae4",
                    "#1d7804",
                    "#1a8cec",
                    "#50e310",
                    "#fcae13"

                ];

                $.post("engine/chart_data_pie_tires_brand.php", {doc_date: "1" ,branch: "2" }, function (data) {
                    console.log(data);
                    let label = [];
                    let total = [];
                    for (let i in data) {
                        label.push(data[i].BRN_CODE);
                        total.push(data[i].TRD_G_KEYIN);
                        //alert(label);
                    }

                    new Chart("myChart2", {
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

<?php } ?>

