<?php
include('includes/Header.php');

// ตรวจสอบว่า session มีการล็อกอินหรือไม่
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index");
    exit();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบค้นหา</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="vendor/date-picker-1.9/css/bootstrap-datepicker.css">
</head>
<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <?php include('includes/Side-Bar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Top Bar -->
            <?php include('includes/Top-Bar.php'); ?>

            <!-- Main Container -->
            <div class="container-fluid" id="container-wrapper">

                <!-- Breadcrumb -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><?php echo urldecode($_GET['s']); ?></h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo $_SESSION['dashboard_page']; ?>">Home</a></li>
                        <li class="breadcrumb-item"><?php echo urldecode($_GET['m']); ?></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo urldecode($_GET['s']); ?></li>
                    </ol>
                </div>

                <!-- Search Form -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="from_data" method="post"
                                      action="export_process/export_data_sale_return_document_cp3.php"
                                      enctype="multipart/form-data">

                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <label for="customer_name"
                                                       class="control-label">ค้นหาตามชื่อลูกค้า</label>
                                                <i class="fa fa-address-card"
                                                   aria-hidden="true"></i>
                                                <input type="text" class="form-control"
                                                       id="customer_name"
                                                       name="customer_name"
                                                       placeholder="">
                                            </div>

                                            <div class="col-sm-3">
                                                <label for="car_no"
                                                       class="control-label">ค้นหาตามทะเบียนรถยนต์</label>
                                                <i class="fa fa-car"
                                                   aria-hidden="true"></i>
                                                <input type="text" class="form-control"
                                                       id="car_no"
                                                       name="car_no"
                                                       placeholder="">
                                            </div>

                                        </div>

                                    </div>


                                    <div class="modal-body">

                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <label for="doc_date_start"
                                                       class="control-label">จากวันที่</label>
                                                <i class="fa fa-calendar"
                                                   aria-hidden="true"></i>
                                                <input type="text" class="form-control datepicker"
                                                       id="doc_date_start"
                                                       name="doc_date_start"
                                                       required="required"
                                                       readonly="true"
                                                       placeholder="จากวันที่">
                                            </div>

                                            <div class="col-sm-3">
                                                <label for="doc_date_to"
                                                       class="control-label">ถึงวันที่</label>
                                                <i class="fa fa-calendar"
                                                   aria-hidden="true"></i>
                                                <input type="text" class="form-control datepicker"
                                                       id="doc_date_to"
                                                       name="doc_date_to"
                                                       required="required"
                                                       readonly="true"
                                                       placeholder="ถึงวันที่">
                                            </div>


                                        </div>

                                        <button type="submit" class="btn btn-success"
                                                id="btnExport"> Export <i
                                                    class="fa fa-check"></i>
                                        </button>

                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Main Container -->

            <!-- Footer -->
            <?php include('includes/Footer.php'); ?>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/select2/dist/js/select2.min.js"></script>
<script src="vendor/date-picker-1.9/js/bootstrap-datepicker.js"></script>

<!--script>
    $(document).ready(function () {
        let today = new Date();
        let doc_date = getDay2Digits(today) + "-" + getMonth2Digits(today) + "-" + today.getFullYear();
        $('#doc_date_start').val(doc_date);
        $('#doc_date_to').val(doc_date);
    });
</script-->

<!--script>
    $(document).ready(function () {
        $('#doc_date_start').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            language: "th",
            autoclose: true
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#doc_date_to').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            language: "th",
            autoclose: true
        });
    });
</script-->

<script>
    $(document).ready(function () {
        function formatDate(date) {
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        let today = new Date();
        let firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

        // กำหนดค่าให้ฟิลด์วันที่
        $('#doc_date_start').val(formatDate(firstDayOfMonth)); // วันที่ 1 ของเดือนปัจจุบัน
        $('#doc_date_to').val(formatDate(today)); // วันที่ปัจจุบัน

        // เปิดใช้งาน datepicker
        $('.datepicker').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            language: "th",
            autoclose: true,
            todayBtn: true
        });
    });
</script>



</body>
</html>
