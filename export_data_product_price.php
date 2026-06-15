<?php
include('includes/Header.php');
include('config/connect_sqlserver.php');

if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {

    $sql_cat = "SELECT ICCAT_CODE, ICCAT_NAME FROM ICCAT 
            WHERE ICCAT_CODE NOT IN ('TATA-001', 'RR.เสื้อผ้า', 'TATA-006')
            ORDER BY ICCAT_CODE";
    $stmt_cat = $conn_sqlsvr->prepare($sql_cat);
    $stmt_cat->execute();
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <!DOCTYPE html>
    <html lang="th">

    <body id="page-top">
    <div id="wrapper">
        <?php
        include('includes/Side-Bar.php');
        ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php
                include('includes/Top-Bar.php');
                ?>

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800"><?php echo urldecode($_GET['s']) ?></h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo $_SESSION['dashboard_page'] ?>">Home</a>
                            </li>
                            <li class="breadcrumb-item"><?php echo urldecode($_GET['m']) ?></li>
                            <li class="breadcrumb-item active"
                                aria-current="page"><?php echo urldecode($_GET['s']) ?></li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-12">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                </div>
                                <div class="card-body">
                                    <section class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-12 col-md-offset-2">
                                                <div class="panel">
                                                    <div class="panel-body">

                                                        <form id="from_data" method="post"
                                                              action="export_process/export_data_product_prices.php"
                                                              enctype="multipart/form-data">

                                                            <div class="modal-body">

                                                                <div class="modal-body">
                                                                    <div class="form-group row">

                                                                        <div class="form-group col-md-12">
                                                                            <label for="price_code"
                                                                                   class="control-label">Select Price Code</label>
                                                                            <select id="price_code" name="price_code"
                                                                                    class="form-control"
                                                                                    data-live-search="true"
                                                                                    title="Please select">
                                                                                <option>ALL</option>
                                                                                <option>SAC</option>
                                                                                <option>BTC</option>
                                                                                <option>COCKPIT</option>
                                                                            </select>
                                                                        </div>


                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <div class="form-group col-md-12">
                                                                            <label for="iccat_code" class="control-label text-primary"><b>เลือกกลุ่มสินค้า (ICCAT_CODE)</b></label>
                                                                            <div class="row">
                                                                                <div class="col-md-12 mb-2">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" id="select_all">
                                                                                        <label class="custom-control-label" for="select_all"><b>เลือกทั้งหมด</b></label>
                                                                                    </div>
                                                                                </div>
                                                                                <?php foreach ($categories as $cat) { ?>
                                                                                    <div class="col-md-4 mb-2">
                                                                                        <div class="custom-control custom-checkbox">
                                                                                            <input type="checkbox" name="iccat_code[]" 
                                                                                                   value="<?php echo $cat['ICCAT_CODE']; ?>" 
                                                                                                   class="custom-control-input iccat_checkbox" 
                                                                                                   id="cat_<?php echo $cat['ICCAT_CODE']; ?>">
                                                                                            <label class="custom-control-label" for="cat_<?php echo $cat['ICCAT_CODE']; ?>">
                                                                                                <?php echo $cat['ICCAT_CODE'] . " - " . $cat['ICCAT_NAME']; ?>
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                             </div>

                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" id="id"/>
                                                                <input type="hidden" name="save_status"
                                                                       id="save_status"/>
                                                                <input type="hidden" name="action" id="action"
                                                                       value=""/>
                                                                <button type="submit" class="btn btn-success"
                                                                        id="btnExport"> Export <i
                                                                            class="fa fa-check"></i>
                                                                </button>
                                                                <!--button type="button" class="btn btn-danger"
                                                                        id="btnClose">Close <i
                                                                            class="fa fa-times"></i>
                                                                </button-->
                                                            </div>


                                                        </form>


                                                        <div id="result"></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.col-md-8 col-md-offset-2 -->
                                        </div>
                                        <!-- /.row -->

                                    </section>


                                </div>

                            </div>

                        </div>

                    </div>
                    <!--Row-->

                    <!-- Row -->

                </div>

                <!---Container Fluid-->

            </div>

            <?php
            include('includes/Modal-Logout.php');
            include('includes/Footer.php');
            ?>

        </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Select2 -->
    <script src="vendor/select2/dist/js/select2.min.js"></script>
    <!-- Bootstrap Datepicker -->
    <script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap Touchspin -->
    <script src="vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js"></script>
    <!-- ClockPicker -->
    <script src="vendor/clock-picker/clockpicker.js"></script>
    <!-- RuangAdmin Javascript -->
    <script src="js/myadmin.min.js"></script>
    <!-- Javascript for this page -->

    <script src="vendor/date-picker-1.9/js/bootstrap-datepicker.js"></script>
    <script src="vendor/date-picker-1.9/locales/bootstrap-datepicker.th.min.js"></script>
    <!--link href="vendor/date-picker-1.9/css/date_picker_style.css" rel="stylesheet"/-->
    <link href="vendor/date-picker-1.9/css/bootstrap-datepicker.css" rel="stylesheet"/>

    <script src="js/MyFrameWork/framework_util.js"></script>

    <script src="js/util.js"></script>

    <script>
        $(document).ready(function () {
            let today = new Date();
            let doc_date = getDay2Digits(today) + "-" + getMonth2Digits(today) + "-" + today.getFullYear();
            $('#doc_date_start').val(doc_date);
            $('#doc_date_to').val(doc_date);

            // Select All logic
            $('#select_all').on('click', function () {
                if (this.checked) {
                    $('.iccat_checkbox').each(function () {
                        this.checked = true;
                    });
                } else {
                    $('.iccat_checkbox').each(function () {
                        this.checked = false;
                    });
                }
            });

            $('.iccat_checkbox').on('click', function () {
                if ($('.iccat_checkbox:checked').length == $('.iccat_checkbox').length) {
                    $('#select_all').prop('checked', true);
                } else {
                    $('#select_all').prop('checked', false);
                }
            });
        });
    </script>

    <script>
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
    </script>

    </body>

    </html>

<?php } ?>