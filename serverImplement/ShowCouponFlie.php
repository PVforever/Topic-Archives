<?php
    require_once('../linkSettings/conn.php');
    require_once('CRUD_Main_Class.php');
    require_once('../linkSettings/coupon_basic.php');

    $couponDao = new couponDao();

    // $id = $_POST['id'];
    // $id = $_GET['id'];
    $id = 1;
    $row = $couponDao ->findById($id);

    // echo implode($row);

    

    print_r($row);
    // $row[0]['coupon_icon'] = base64_decode($row[0]['coupon_icon']);

    // $json = json_encode($row[0]['coupon_icon'], JSON_UNESCAPED_UNICODE);
    // if ($json !== false) {
    //     echo $json;
    // } else {
    //     echo "JSON encoding failed: " . json_last_error_msg();
    // }

    // echo json_encode($row);
    // print_r($row);
?>