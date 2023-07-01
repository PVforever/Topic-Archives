<?php 
    // 依照前端送來的suspended[]來更新User的suspended欄位
    require_once('../linkSettings/conn.php');
    require_once('CRUD_Main_Class.php');
    require_once('../linkSettings/coupon_basic.php');
    $couponDao = new couponDao();
    $e_sta =$_POST['value'];
    $id =$_POST['id'];

    $couponDao ->updateEnabled_stateById($e_sta,$id);
  
    // if (!isset($_POST['enabled_state'])){
    //     if (isset($_POST['enabled_state'])){           // 如果前端有送suspended欄位的值
    //        $userDao->resetenabled_state();   // 將User表格所有紀錄的suspended欄位先清為空白
    //        $arr = $_POST['enabled_state'];
    //        for($n = 0; $n < count($arr); $n++){    // 依照前端送來停權的suspended欄位更新對應的紀錄
    //             $userDao->updateEnabled_stateById($arr[$n]);
    //        }
    //     } else {
    //        ;
    //     }
    // } else {
    //     ;
    // }
    // header("Location:index.php");

?>