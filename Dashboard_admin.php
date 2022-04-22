<?php
include('includes/Header.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {

    include("config/connect_db.php");

    ?>

    <!DOCTYPE html>
    <html lang="th">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <body id="page-top" onload="showGraph_Cockpit_Daily();showGraph_Tires_Brand();">
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
                                            <!--div class="mt-2 mb-0 text-muted text-xs">
                                                <span class="text-success mr-2"><i
                                                            class="fa fa-arrow-up"></i></span>
                                                <span>Since last month</span>
                                            </div-->
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
                                            <!--div class="mt-2 mb-0 text-muted text-xs">
                                                <span class="text-success mr-2"><i
                                                            class="fas fa-arrow-up"></i> 12%</span>
                                                <span>Since last years</span>
                                            </div-->
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
                                            <!--div class="mt-2 mb-0 text-muted text-xs">
                                                <span class="text-success mr-2"><i
                                                            class="fas fa-arrow-up"></i> 20.4%</span>
                                                <span>Since last month</span>
                                            </div-->
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
                                    สถิติ ยอดขายรายวัน Cockpit แต่ละสาขา วันที่
                                    <?php echo date("d-m-Y"); ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">ปี <?php echo date("Y"); ?></h5>
                                    <canvas id="myChart" width="200" height="200"></canvas>
                                </div>
                                <div class="card-body">
                                    <table id="example" class="display table table-striped table-bordered"
                                           cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>สาขา</th>
                                            <th>ยอดขาย</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th>สาขา</th>
                                            <th>ยอดขาย</th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                        $date = date("d/m/Y");
                                        $sql = "SELECT BRANCH,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as  TRD_G_KEYIN
                                                      FROM ims_product_sale_cockpit 
                                                      WHERE DI_DATE = '" .$date . "'
                                                      GROUP BY  BRANCH
                                                      ORDER BY BRANCH";

                                        $statement = $conn->query($sql);
                                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($results as $row) { ?>

                                        <tr>
                                            <td><?php echo htmlentities($row['BRANCH']); ?></td>
                                            <td><?php echo htmlentities($row['TRD_G_KEYIN']); ?></td>
                                        <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    สถิติ มูลค่าการขายยาง Cockpit แต่ละยี่ห้อ
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">ปี <?php echo date("Y"); ?></h5>
                                    <canvas id="myChart2" width="200" height="200"></canvas>
                                </div>
                                <div class="card-body">
                                    <table id="example" class="display table table-striped table-bordered"
                                           cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>ยี่ห้อ</th>
                                            <th>จำนวน (เส้น)</th>
                                            <th>ยอดขาย</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th>ยี่ห้อ</th>
                                            <th>จำนวน (เส้น)</th>
                                            <th>ยอดขาย</th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                        $year = date("Y");
                                        $sql =  "SELECT BRN_CODE,BRN_NAME,SKU_CAT,ICCAT_NAME,sum(CAST(TRD_QTY AS DECIMAL(10,2))) as  TRD_QTY,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as TRD_G_KEYIN 
                                        FROM ims_product_sale_cockpit
                                        WHERE SKU_CAT IN ('2SAC01','2SAC02','2SAC03','2SAC02','2SAC04','2SAC05','2SAC06','2SAC07','2SAC08','2SAC09','2SAC10','2SAC11','2SAC12','2SAC13','2SAC14','2SAC15')
                                        AND DI_YEAR = '" . $year . "'
                                        GROUP BY BRN_CODE,BRN_NAME,SKU_CAT,ICCAT_NAME
                                        ORDER BY SKU_CAT ";

                                        $myfile = fopen("sql.txt", "w") or die("Unable to open file!");
                                        fwrite($myfile,  $sql);
                                        fclose($myfile);

                                        $statement = $conn->query($sql);
                                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($results as $row) { ?>

                                        <tr>
                                            <td><?php echo htmlentities($row['BRN_NAME']); ?></td>
                                            <td><?php echo htmlentities($row['TRD_QTY']); ?></td>
                                            <td><?php echo htmlentities($row['TRD_G_KEYIN']); ?></td>
                                            <?php } ?>

                                        </tbody>
                                    </table>
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
            GET_DATA("ims_customer_ar", "3");
            GET_DATA("ims_supplier", "4");

            setInterval(function () {
                GET_DATA("ims_order_master", "1");
                GET_DATA("ims_product", "2");
                GET_DATA("ims_customer_ar", "3");
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

                $.post("engine/chart_data_pie_tires_brand.php", {doc_date: "1", branch: "2"}, function (data) {
                    console.log(data);
                    let label = [];
                    let label_name = [];
                    let total = [];
                    for (let i in data) {
                        label.push(data[i].BRN_CODE);
                        label_name.push(data[i].BRN_NAME);
                        total.push(data[i].TRD_G_KEYIN);
                        //alert(label);
                    }

                    new Chart("myChart2", {
                        type: "pie",
                        data: {
                            labels: label_name,
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

    <script>
        function showGraph_Cockpit_Daily() {
            {

                //let data_date = $("#data_date").val();

                let backgroundColor = '#0a4dd3';
                let borderColor = '#46d5f1';

                let hoverBackgroundColor = '#1712bf';
                let hoverBorderColor = '#a2a1a3';

                $.post("engine/chart_data_cockpit_daily.php", {date: "2"}, function (data) {
                    console.log(data);
                    let branch = [];
                    let total = [];
                    for (let i in data) {
                        branch.push(data[i].BRANCH);
                        total.push(data[i].TRD_G_KEYIN);
                    }

                    let chartdata = {
                        labels: branch,
                        datasets: [{
                            label: 'ยอดขายรายวัน รวม VAT (Daily)',
                            backgroundColor: backgroundColor,
                            borderColor: borderColor,
                            hoverBackgroundColor: hoverBackgroundColor,
                            hoverBorderColor: hoverBorderColor,
                            data: total
                        }]
                    };
                    let graphTarget = $('#myChart');
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

<?php } ?>

