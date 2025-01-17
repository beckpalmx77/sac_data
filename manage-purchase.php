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
                        <input type="hidden" id="main_menu" value="<?php echo urldecode($_GET['m']) ?>">
                        <input type="hidden" id="sub_menu" value="<?php echo urldecode($_GET['s']) ?>">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo $_SESSION['dashboard_page']?>">Home</a></li>
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

                                        <div class="col-md-12 col-md-offset-2">
                                            <label for="name_t"
                                                   class="control-label"><b>เพิ่ม <?php echo urldecode($_GET['s']) ?></b></label>

                                            <button type='button' name='btnAdd' id='btnAdd'
                                                    class='btn btn-primary btn-xs'>Add
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>

                                        <div class="col-md-12 col-md-offset-2">
                                            <table id='TableRecordList' class='display dataTable'>
                                                <thead>
                                                <tr>
                                                    <th>เลขที่เอกสาร</th>
                                                    <th>ชื่อผู้ขาย</th>
                                                    <th>วันที่</th>
                                                    <th>สถานะ</th>
                                                    <th>Action</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
                                                    <th>เลขที่เอกสาร</th>
                                                    <th>ชื่อผู้ขาย</th>
                                                    <th>วันที่</th>
                                                    <th>สถานะ</th>
                                                    <th>Action</th>
                                                    <th>Action</th>
                                                </tr>
                                                </tfoot>
                                            </table>

                                            <div id="result"></div>

                                        </div>

                                        <div class="modal fade" id="recordModal">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Modal title</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-hidden="true">×
                                                        </button>
                                                    </div>
                                                    <form method="post" id="recordForm">
                                                        <input type="hidden" id="KeyAddData" name="KeyAddData" value="">
                                                        <div class="modal-body">
                                                            <div class="modal-body">

                                                                <div class="form-group row">
                                                                    <div class="col-sm-6">
                                                                        <label for="doc_no"
                                                                               class="control-label">เลขที่เอกสาร</label>
                                                                        <input type="text" class="form-control"
                                                                               id="doc_no"
                                                                               name="doc_no"
                                                                               readonly="true"
                                                                               placeholder="สร้างอัตโนมัติ">
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <label for="doc_date"
                                                                               class="control-label">วันที่เอกสาร</label>
                                                                        <input type="text" class="form-control"
                                                                               id="doc_date"
                                                                               name="doc_date"
                                                                               readonly="true"
                                                                               placeholder="วันที่เอกสาร">
                                                                    </div>

                                                                </div>

                                                                <div class="form-group row">
                                                                    <input type="hidden" class="form-control"
                                                                           id="supplier_id"
                                                                           name="supplier_id">
                                                                    <div class="col-sm-12">
                                                                        <label for="supplier_name"
                                                                               class="control-label">ชื่อผู้ขาย</label>
                                                                        <input type="text" class="form-control"
                                                                               id="supplier_name"
                                                                               name="supplier_name"
                                                                               readonly="true"
                                                                               placeholder="ชื่อผู้ขาย">
                                                                    </div>
                                                                </div>

                                                                <table cellpadding="0" cellspacing="0" bpurchase="0"
                                                                       class="display"
                                                                       id="TablePurchaseDetailList"
                                                                       width="100%">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>สินค้า</th>
                                                                        <th>จำนวน</th>
                                                                        <th>หน่วยนับ</th>
                                                                    </tr>
                                                                    </thead>
                                                                </table>

                                                                <div class="form-group">
                                                                    <label for="status"
                                                                           class="control-label">Status</label>
                                                                    <select id="status" name="status"
                                                                            class="form-control"
                                                                            data-live-search="true"
                                                                            title="Please select">
                                                                        <option>Active</option>
                                                                        <option>Inactive</option>
                                                                    </select>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="id" id="id"/>
                                                            <input type="hidden" name="action" id="action"
                                                                   value=""/>
                                                            <span class="icon-input-btn">
                                                                <i class="fa fa-check"></i>
                                                            <input type="submit" name="save" id="save"
                                                                   class="btn btn-primary" value="Save"/>
                                                            </span>
                                                            <button type="button" class="btn btn-danger"
                                                                    data-dismiss="modal">Close <i
                                                                        class="fa fa-window-close"></i>
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>


                                        <div class="modal fade" id="SearchCusModal">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Modal title</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-hidden="true">×
                                                        </button>
                                                    </div>

                                                    <div class="container"></div>
                                                    <div class="modal-body">

                                                        <div class="modal-body">

                                                            <table cellpadding="0" cellspacing="0" bpurchase="0"
                                                                   class="display"
                                                                   id="TableSupplierList"
                                                                   width="100%">
                                                                <thead class="thead-light">
                                                                <tr>
                                                                    <th>รหัสลูกค้า</th>
                                                                    <th>ชื่อผู้ขาย</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                                </thead>
                                                                <tfoot>
                                                                <tr>
                                                                    <th>รหัสลูกค้า</th>
                                                                    <th>ชื่อผู้ขาย</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

    <script src="js/modal/show_supplier_modal.js"></script>
    <!-- Page level plugins -->

    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css"/-->

    <script src="vendor/datatables/v11/bootbox.min.js"></script>
    <script src="vendor/datatables/v11/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="vendor/datatables/v11/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="vendor/datatables/v11/buttons.dataTables.min.css"/>

    <script src="vendor/date-picker-1.9/js/bootstrap-datepicker.js"></script>
    <script src="vendor/date-picker-1.9/locales/bootstrap-datepicker.th.min.js"></script>
    <!--link href="vendor/date-picker-1.9/css/date_picker_style.css" rel="stylesheet"/-->
    <link href="vendor/date-picker-1.9/css/bootstrap-datepicker.css" rel="stylesheet"/>

    <script src="js/popup.js"></script>

    <style>

        .icon-input-btn {
            display: inline-block;
            position: relative;
        }

        .icon-input-btn input[type="submit"] {
            padding-left: 2em;
        }

        .icon-input-btn .fa {
            display: inline-block;
            position: absolute;
            left: 0.65em;
            top: 30%;
        }
    </style>

    <script>
        $(document).ready(function () {
            $('#doc_date').datepicker({
                format: "yyyy-mm-dd",
                todayHighlight: true,
                language: "th",
                autoclose: true
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $(".icon-input-btn").each(function () {
                let btnFont = $(this).find(".btn").css("font-size");
                let btnColor = $(this).find(".btn").css("color");
                $(this).find(".fa").css({'font-size': btnFont, 'color': btnColor});
            });
        });
    </script>

    <script>

        $("#doc_no").blur(function () {
            let method = $('#action').val();
            if (method === "ADD") {
                let doc_no = $('#doc_no').val();
                let supplier_id = $('#doc_no').val();
                let formData = {action: "SEARCH", doc_no: doc_no, supplier_id: supplier_id};
                $.ajax({
                    url: 'model/manage_purchase_process.php',
                    method: "POST",
                    data: formData,
                    success: function (data) {
                        if (data == 2) {
                            alert("Duplicate มีข้อมูลนี้แล้วในระบบ กรุณาตรวจสอบ");
                        }
                    }
                })
            }
        });

    </script>

    <script>
        $(document).ready(function () {
            let formData = {action: "GET_PURCHASE", sub_action: "GET_MASTER"};
            let dataRecords = $('#TableRecordList').DataTable({
                'columnDefs': [{"orderSequence": ["desc", "asc"]}],
                'lengthMenu': [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
                'language': {
                    search: 'ค้นหา', lengthMenu: 'แสดง _MENU_ รายการ',
                    info: 'หน้าที่ _PAGE_ จาก _PAGES_',
                    infoEmpty: 'ไม่มีข้อมูล',
                    zeroRecords: "ไม่มีข้อมูลตามเงื่อนไข",
                    infoFiltered: '(กรองข้อมูลจากทั้งหมด _MAX_ รายการ)',
                    paginate: {
                        previous: 'ก่อนหน้า',
                        last: 'สุดท้าย',
                        next: 'ต่อไป'
                    }
                },
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': 'model/manage_purchase_process.php',
                    'data': formData
                },
                'columns': [
                    {data: 'doc_no'},
                    {data: 'supplier_name'},
                    {data: 'doc_date'},
                    {data: 'status'},
                    {data: 'update'},
                    {data: 'delete'}
                ]
            });

            <!-- *** FOR SUBMIT FORM *** -->
            $("#recordModal").on('submit', '#recordForm', function (event) {
                event.preventDefault();
                $('#save').attr('disabled', 'disabled');
                let formData = $(this).serialize();
                $.ajax({
                    url: 'model/manage_purchase_process.php',
                    method: "POST",
                    data: formData,
                    success: function (data) {
                        alertify.success(data);
                        $('#recordForm')[0].reset();
                        $('#recordModal').modal('hide');
                        $('#save').attr('disabled', false);
                        dataRecords.ajax.reload();
                    }
                })
            });
            <!-- *** FOR SUBMIT FORM *** -->
        });
    </script>

    <script>

        $("#TableRecordList").on('click', '.delete', function () {
            let id = $(this).attr("id");
            let formData = {action: "GET_DATA", id: id};
            let table_name = "v_purchase_detail";
            $.ajax({
                type: "POST",
                url: 'model/manage_purchase_process.php',
                dataType: "json",
                data: formData,
                success: function (response) {
                    let len = response.length;
                    for (let i = 0; i < len; i++) {
                        let id = response[i].id;
                        let doc_no = response[i].doc_no;
                        let doc_date = response[i].doc_date;
                        let supplier_id = response[i].supplier_id;
                        let supplier_name = response[i].supplier_name;
                        let status = response[i].status;

                        $('#recordModal').modal('show');

                        Load_Data_Detail(doc_no, table_name);

                        $('#id').val(id);
                        $('#doc_no').val(doc_no);
                        $('#doc_date').val(doc_date);
                        $('#supplier_id').val(supplier_id);
                        $('#supplier_name').val(supplier_name);
                        $('#status').val(status);
                        $('.modal-title').html("<i class='fa fa-minus'></i> Delete Record");
                        $('#action').val('DELETE');
                        $('#save').val('Confirm Delete');
                    }
                },
                error: function (response) {
                    alertify.error("error : " + response);
                }
            });
        });

    </script>

    <script>

        $("#btnAdd").click(function () {
            let main_menu = document.getElementById("main_menu").value;
            let sub_menu = document.getElementById("sub_menu").value;
            let url = "manage_purchase_data?title=รายการซื้อสินค้า (Product Purchase)"
                + '&main_menu=' + main_menu + '&sub_menu=' + sub_menu
                + '&action=ADD';
            OpenPopupCenter(url, "", "");
        });

    </script>

    <script>
        $("#TableRecordList").on('click', '.update', function () {

            let id = $(this).attr("id");
            let main_menu = document.getElementById("main_menu").value;
            let sub_menu = document.getElementById("sub_menu").value;
            //alert(id);
            let formData = {action: "GET_DATA", id: id};
            $.ajax({
                type: "POST",
                url: 'model/manage_purchase_process.php',
                dataType: "json",
                data: formData,
                success: function (response) {
                    let len = response.length;
                    for (let i = 0; i < len; i++) {
                        let doc_no = response[i].doc_no;
                        let doc_date = response[i].doc_date;
                        let supplier_id = response[i].supplier_id;
                        let supplier_name = response[i].supplier_name;
                        let url = "manage_purchase_data?title=รายการซื้อสินค้า (Product Purchase)"
                            + '&main_menu=' + main_menu + '&sub_menu=' + sub_menu
                            + '&doc_no=' + doc_no + '&doc_date=' + doc_date
                            + '&supplier_id=' + supplier_id
                            + '&supplier_name=' + supplier_name
                            + '&action=UPDATE';
                        OpenPopupCenter(url, "", "");
                    }
                },
                error: function (response) {
                    alertify.error("error : " + response);
                }
            });
        });
    </script>

    <script>
        function Load_Data_Detail(doc_no, table_name) {

            $('#TablePurchaseDetailList').DataTable().clear().destroy();

            let formData = {action: "GET_PURCHASE_DETAIL", sub_action: "GET_MASTER", doc_no: doc_no, table_name: table_name};
            let dataRecords = $('#TablePurchaseDetailList').DataTable({
                "paging": false,
                "purchaseing": false,
                'info': false,
                "searching": false,
                'lengthMenu': [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
                'language': {
                    search: 'ค้นหา', lengthMenu: 'แสดง _MENU_ รายการ',
                    info: 'หน้าที่ _PAGE_ จาก _PAGES_',
                    infoEmpty: 'ไม่มีข้อมูล',
                    zeroRecords: "ไม่มีข้อมูลตามเงื่อนไข",
                    infoFiltered: '(กรองข้อมูลจากทั้งหมด _MAX_ รายการ)',
                    paginate: {
                        previous: 'ก่อนหน้า',
                        last: 'สุดท้าย',
                        next: 'ต่อไป'
                    }
                },
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': 'model/manage_purchase_detail_process.php',
                    'data': formData
                },
                'columns': [
                    {data: 'line_no'},
                    {data: 'product_name'},
                    {data: 'quantity'},
                    {data: 'unit_name'}
                ]
            });
        }
    </script>

    </body>
    </html>

<?php } ?>


