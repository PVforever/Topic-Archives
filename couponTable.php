<?php
session_start(); 
?>
<?php require_once('/conn.php'); ?>
<?php require_once('/CRUD_Main_Class.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="my-5 shadow rounded-3 text-center">
                    <div class="d-flex p-3">
                        <button id="buttonAdd" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#couponModal">新增</button>
                        <div class="ui-widget ms-auto">
                            <label for="tags">搜尋: </label>
                            <input id="tags" placeholder="名稱搜尋">
                        </div>
                    </div>
                        <!-- <img id="img1" src="images/loading.gif" style="width:50px;display:none"> -->
                    <table id="couponTable" class="table table-dark table-hover">
                        <thead class="text-nowrap">
                            <tr>
                                <th>編號</th>
                                <th>名稱</th>
                                <th>圖片</th>
                                <th>代碼</th> 
                                <th style="display:none">折抵金額</th>
                                <th style="display:none">金額限制</th>
                                <th>數量</th>
                                <th style="display:none">限領數量</th>          
                                <th>優惠券類型</th>              
                                <th>使用類型</th>  
                                <th style="display:none">疊加使用</th>  
                                <th style="display:none">賣家等級</th>  
                                <th>開始時間</th>             
                                <th>結束時間</th> 
                                <th>編輯</th>
                            </tr>
                        </thead>
                        <tbody class="">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-1"></div>
        </div>

        <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="couponModalLabel">資料修改</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="couponForm">
                            <div class="mb-3">
                                <input type="hidden" id="id" name="id">
                                <label for="name" class="col-form-label">優惠券名稱:</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                            <div class="mb-3">
                                <label for="pic" class="col-form-label">圖片:</label>
                                <input type="text" class="form-control" name="pic" id="pic">
                            </div>
                            <div class="mb-3">
                                <label for="code" class="col-form-label">代碼:</label>
                                <input type="text" class="form-control" name="code" id="code">
                            </div>
                            <div class="mb-3">
                                <label for="amo" class="col-form-label">折抵金額:</label>
                                <input type="text" class="form-control" name="amo" id="amo">
                            </div>
                            <div class="mb-3">
                                <label for="min" class="col-form-label">金額限制:</label>
                                <input type="text" class="form-control" name="min" id="min">
                            </div>
                            <div class="mb-3">
                                <label for="qua" class="col-form-label">優惠券數量:</label>
                                <input type="text" class="form-control" name="qua" id="qua">
                            </div>
                            <div class="mb-3">
                                <label for="per" class="col-form-label">限領:</label>
                                <input type="text" class="form-control" name="per" id="per">
                            </div>
                            <div class="mb-3">
                                <label for="c_type" class="col-form-label">優惠券類型:</label>
                                <input type="text" class="form-control" name="c_type" id="c_type">
                            </div>
                            <div class="mb-3">
                                <label for="u_type" class="col-form-label">使用類型:</label>
                                <input type="text" class="form-control" name="u_type" id="u_type">
                            </div>
                            <div class="mb-3">
                                <label for="ove" class="col-form-label">疊加使用:</label>
                                <input type="text" class="form-control" name="ove" id="ove">
                            </div>
                            <div class="mb-3">
                                <label for="lv" class="col-form-label">會員領取等級:</label>
                                <input type="text" class="form-control" name="lv" id="lv">
                            </div>
                            <div class="mb-3">
                                <label for="s_time" class="col-form-label">開始時間:</label>
                                <input type="date" class="form-control" name="s_time" id="s_time">
                            </div>
                            <div class="mb-3">
                                <label for="e_time" class="col-form-label">結束時間:</label>
                                <input type="date" class="form-control" name="e_time" id="e_time">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                        <button id="buttonUpdate" type="button" class="btn btn-primary">修改</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
//

</html>