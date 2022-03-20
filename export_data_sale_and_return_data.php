<?php
include('includes/Header.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {
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

                                                        <form id="from_data">

                                                            <div class="modal-body">

                                                                <div class="modal-body">
                                                                    <div class="form-group row">

                                                                        <div class="col-sm-3">
                                                                            <label for="doc_date_start"
                                                                                   class="control-label">วันที่เริ่มต้น</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="doc_date_start"
                                                                                   name="doc_date_start"
                                                                                   required="required"
                                                                                   readonly="true"
                                                                                   placeholder="วันที่เริ่มต้น">
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <label for="doc_date_to"
                                                                                   class="control-label">วันที่เสิ้นสุด</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="doc_date_to"
                                                                                   name="doc_date_to"
                                                                                   required="required"
                                                                                   readonly="true"
                                                                                   placeholder="วันที่เสิ้นสุด">
                                                                        </div>


                                                                    </div>
                                                                </div>


                                                            </div>

                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" id="id"/>
                                                                <input type="hidden" name="save_status" id="save_status"/>
                                                                <input type="hidden" name="action" id="action"
                                                                       value=""/>
                                                                <button type="button" class="btn btn-success"
                                                                        id="btnSave">Export <i
                                                                            class="fa fa-check"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger"
                                                                        id="btnClose">Close <i
                                                                            class="fa fa-window-close"></i>
                                                                </button>
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

    <script>
        $(document).ready(function () {
            $("form").on("submit", function (event) {
                event.preventDefault();
                let formValues = $(this).serialize();
                $.post("model/manage_account_process.php", formValues, function (response) {
                    if (response == 1) {
                        document.getElementById("from_data").reset();
                        alertify.success("บันทึกข้อมูลเรียบร้อย Save Data Success");

                    } else if (response == 2) {
                        alertify.error("ไม่สามารถบันทึกข้อมูลได้ มี User นี้แล้ว ");
                    } else {
                        alertify.error("ไม่สามารถบันทึกข้อมูลได้ DB Error ");
                    }
                });
            });
        });
    </script>

    <script>

        $('#email').blur(function () {

            let action = "GET_COUNT_RECORDS_COND";
            let table_name = "ims_user";
            let cond = " WHERE email = '" + $('#email').val() + "'";
            let formData = {action: action, table_name: table_name, cond: cond};
            $.ajax({
                type: "POST",
                url: 'model/manage_general_data.php',
                data: formData,
                success: function (response) {
                    if (response > 0) {
                        alertify.error("มี User Email นี้ในระบบแล้วโปรดใช้ User อื่น");
                        $('#email').val("");
                    }
                },
                error: function (response) {
                    alertify.error("error : " + response);
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