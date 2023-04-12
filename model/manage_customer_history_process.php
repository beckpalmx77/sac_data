<?php
session_start();
error_reporting(0);
date_default_timezone_set("Asia/Bangkok");
// include('../config/connect_db.php');
include("../config/connect_sqlserver.php");
include('../config/lang.php');


if ($_POST["action"] === 'GET_HISTORY_DETAIL') {

    $str_qry = "
    
SELECT 
TRANSTKD.TRD_KEY , 
ADDRBOOK.ADDB_KEY , 
ADDRBOOK.ADDB_BRANCH , 
ADDRBOOK.ADDB_SEARCH ,
ADDRBOOK.ADDB_ADDB_1 , 
ADDRBOOK.ADDB_ADDB_2 , 
ADDRBOOK.ADDB_COMPANY ,
DOCINFO.DI_REF , 
DOCINFO.DI_DATE ,
DAY(DI_DATE) AS DI_DAY ,
MONTH(DI_DATE) AS DI_MONTH ,
YEAR(DI_DATE) AS DI_YEAR ,
TRANSTKH.TRH_DI,
SKUMASTER.SKU_CODE ,
SKUMASTER.SKU_NAME ,
TRANSTKD.TRD_QTY,
TRANSTKD.TRD_U_PRC,
TRANSTKD.TRD_B_SELL,
TRANSTKD.TRD_B_VAT,
TRANSTKD.TRD_B_AMT 

FROM 
ADDRBOOK,
ARADDRESS,
ARDETAIL,
DOCINFO ,
TRANSTKH ,
TRANSTKD ,
SKUMASTER

WHERE 

-- ADDRBOOK.ADDB_COMPANY like '%พงษ์ศักดิ์ %'   AND  
-- ADDRBOOK.ADDB_BRANCH  not like '' AND 
-- ADDRBOOK.ADDB_SEARCH like '%%' AND 

(ADDRBOOK.ADDB_KEY = ARADDRESS.ARA_ADDB) AND 
(ARDETAIL.ARD_AR = ARADDRESS.ARA_AR) AND 
(DOCINFO.DI_KEY = ARDETAIL.ARD_DI) AND 
(DOCINFO.DI_KEY = TRANSTKH.TRH_DI) AND 
(TRANSTKH.TRH_KEY = TRANSTKD.TRD_TRH) AND 
(TRANSTKD.TRD_SKU = SKUMASTER.SKU_KEY) 

";

    ## Read value
    $table_name = $_POST['table_name'];
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $searchArray = array();

## Search
    $searchQuery = " ";
    if ($searchValue != '') {
        $searchQuery = " AND (doc_no LIKE :doc_no or
        doc_date LIKE :doc_date ) ";
        $searchArray = array(
            'doc_no' => "%$searchValue%",
            'doc_date' => "%$searchValue%",
        );
    }

## Total number of records without filtering
    $stmt = $conn->prepare("select count(*) from ADDRBOOK   WHERE ADDRBOOK.ADDB_COMPANY like '%" . $_POST["customer_name"] . "%'" . " or ADDRBOOK.ADDB_SEARCH like '%" . $_POST["car_no"] . "%'");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

## Total number of records with filtering
    $stmt = $conn->prepare("select count(*) from ADDRBOOK   WHERE ADDRBOOK.ADDB_COMPANY like '%" . $_POST["customer_name"] . "%'" . " or ADDRBOOK.ADDB_SEARCH like '%" . $_POST["car_no"] . "%'");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];


    $query_str = "SELECT * FROM " . $table_name . " WHERE doc_no = '" . $_POST["doc_no"] . "'"
        . " ORDER BY line_no ";

    $stmt = $conn->prepare($query_str);
    $stmt->execute();
    $empRecords = $stmt->fetchAll();
    $data = array();

    foreach ($empRecords as $row) {

        if ($_POST['sub_action'] === "GET_MASTER") {
            $data[] = array(
                "id" => $row['id'],
                "doc_no" => $row['doc_no'],
                "doc_date" => $row['doc_date'],
                "line_no" => $row['line_no'],
                "product_id" => $row['product_id'],
                "product_name" => $row['product_name'],
                "quantity" => number_format($row['quantity'], 2),
                "price" => number_format($row['price'], 2),
                "total_price" => number_format($row['total_price'], 2),
                "unit_id" => $row['unit_id'],
                "unit_name" => $row['unit_name'],
                "update" => "<button type='button' name='update' id='" . $row['id'] . "' class='btn btn-info btn-xs update' data-toggle='tooltip' title='Update'>Update</button>",
                "delete" => "<button type='button' name='delete' id='" . $row['id'] . "' class='btn btn-danger btn-xs delete' data-toggle='tooltip' title='Delete'>Delete</button>"
            );
        } else {
            $data[] = array(
                "id" => $row['id'],
                "doc_no" => $row['doc_no'],
                "doc_date" => $row['doc_date'],
                "select" => "<button type='button' name='select' id='" . $row['doc_no'] . "@" . $row['doc_date'] . "' class='btn btn-outline-success btn-xs select' data-toggle='tooltip' title='select'>select <i class='fa fa-check' aria-hidden='true'></i>
</button>",
            );
        }

    }

## Response Return Value
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);


}

