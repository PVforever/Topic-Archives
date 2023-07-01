<?php 
session_start(); 
?>
<?php require_once('linkSettings/conn.php'); ?>
<?php require_once('serverImplement/CRUD_Main_Class.php'); ?>
<?php require_once('linkSettings/coupon_basic.php'); ?>

<?php
$couponDao = new couponDao();

$currentPage = $_SERVER["PHP_SELF"];
$maxRows = 5;    // 每頁顯示幾筆記錄
$pageNum = 0;    // 將要顯示哪一頁的資料(0表示第一頁)
$searchName = "";    // 搜尋優惠券名稱內容

// 設定一個SESSION變數couponListMaxRows，內容為每頁至多顯示之記錄數，
// 其他程式需要此資料。.
$_SESSION['couponListMaxRows'] = $maxRows ;

if (isset($_GET['pageNum'])) {
  $pageNum = $_GET['pageNum'];
}
$startRow = $pageNum * $maxRows;    // 算出將要顯示的分頁是由哪一筆開始(0表示第一筆)

function typeName($row) {
  switch ($row) {
    case 1:
    echo "免運券";
    break;
    case 2:
    echo "註冊禮券";
    break;
    case 3:
    echo "生日禮券";
    break;
    case 4:
    echo "購物禮券";
    break;
    case 5:
    echo "平台禮券";
    break;
  }
}

function useName($row) {
  switch ($row) {
    case 0:
    echo "平台通用";
    break;
    case 1:
    echo "指定書類";
    break;
    case 2:
    echo "指定商品";
    break;
  }
}

function quanumber($row) {
  switch ($row) {
    case $row < 0:
    echo "無限制";
    break;
    case $row > 0:
    echo $row;
    break;
  }
}

// $arr2D = $couponDao->findWithinRange($startRow, $maxRows);
if(!empty($_GET["searchName"])) {// 確定是否存在資料
  $searchName = $_GET["searchName"];
  $arr2D = $couponDao->findWithName($startRow, $maxRows, $searchName);  // 模糊查詢'
  
  // 算出總共有多少筆商品的SQL敘述，coupon表格的總筆數
  // 如果外界有透過GET方法傳入totalRows(表格的總紀錄筆數)
  if (isset($_GET['totalRows'])) {
    $totalRows = $_GET['totalRows'];
  } else {
    // 否則到資料庫讀取『coupon表格的總紀錄筆數』，
    // 放到變數 $totalRows內
    $totalRows  = count($couponDao->findWithName(null, null, $searchName)) ;
    // $totalRows  = count($couponDao->findWithName($startRow, $maxRows, $searchName)) ;
    
  }
  // 計算有幾頁(Page) 0 表示有1頁，1 表示有2頁，
  // 例如：有15筆記錄，每頁3筆, 總共5頁($totalPages的值為4)
  $totalPages = ceil($totalRows/$maxRows)-1;  // 

  $queryString_Recordset1 = "&totalRows=$totalRows&searchName=$searchName";

}else{
  $arr2D = $couponDao->findWithinRange($startRow, $maxRows);
  // 算出總共有多少筆商品的SQL敘述，coupon表格的總筆數
  // 如果外界有透過GET方法傳入totalRows(表格的總紀錄筆數)
  if (isset($_GET['totalRows'])) {
    $totalRows = $_GET['totalRows'];
  } else {
    // 否則到資料庫讀取『coupon表格的總紀錄筆數』，
    // 放到變數 $totalRows內
    $totalRows  = count($couponDao->findAll()) ;
  }

  // 計算有幾頁(Page) 0 表示有1頁，1 表示有2頁，
  // 例如：有15筆記錄，每頁3筆, 總共5頁($totalPages的值為4)
  $totalPages = ceil($totalRows/$maxRows)-1;  // 

  $queryString_Recordset1 = "&totalRows=$totalRows";
}
?>

<!DOCTYPE html >
<html>
<head>
  <meta charset="utf-8" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="css/pixel.css">
  <link rel="stylesheet" href="css/uiSuite.css">
  <title>優惠券管理</title>
  <style>
    a{
      text-decoration: none;
      color: inherit;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="position-relative" style="margin-top: 10vh;">
      <div class="cou-title mb-3">
        <h4 class="d-flex justify-content-center py-4 m-0 fw-bold">優惠券管理</h4>
      </div>
      <div class="drawing-board">
        <div class="d-flex actionList text-nowrap">   
          <div class="d-flex w-100 p-3 pt-4">
            <div class="d-flex me-auto">
              <!-- 使用類型篩選 -->
              <form class="m-0" id="search_post">
                <select class="form-select me-2 w-auto pulldownhandle" aria-label="Default select example" required>
                  <option selected hidden>使用類型</option>
                  <option selected>請選擇</option>
                  <option value="where use_type ='0'">平台通用</option>
                  <option value="where use_type ='1'">指定書類</option>
                  <option value="where use_type ='2'">指定商品</option>
                </select>
              </form>
              <!-- 搜尋 -->
              <form class="m-0 d-flex" action="coupon_index.php" method="get">
                <input class="form-control me-2 serachbox" name="searchName" type="search" placeholder="輸入優惠券名稱" aria-label="Search" value="<?php echo isset($_GET['searchName']) ? $_GET['searchName'] : $searchName ?>">
                                                
                <input class="btn btn-primary searchon search" type="submit" value="搜尋">
              </form>
            </div>
            <!-- 增加優惠券 -->
            <button class="btn add-on"><a href="couponInsert.php"><i class="fa-solid fa-plus"></i></a></button>
          </div>
        </div>
        <div class="text-center w-100">
          <div class="row flex-nowrap px-4 my-4">
            <div class="col formHead py-2 mx-2">編號</div>
            <div class="col formHead py-2 mx-2">名稱</div>
            <div class="col formHead py-2 mx-2">圖片</div>
            <div class="col formHead py-2 mx-2">代碼</div> 
            <!-- <div>折抵金額</div> -->
            <!-- <div>金額限制</div> -->
            <div class="col formHead py-2 mx-2">數量</div>
            <!-- <div>限領數量</div> -->
            <div class="col formHead py-2 mx-2">優惠類型</div>              
            <div class="col formHead py-2 mx-2">使用類型</div>  
            <!-- <div>疊加使用</div> -->
            <!-- <div>賣家等級</div> -->
            <div class="col formHead py-2 mx-2">開始時間</div>             
            <div class="col formHead py-2 mx-2">結束時間</div> 
            <div class="col formHead py-2 mx-2">狀態</div> 
            <div class="col formHead py-2 mx-2">編輯</div>
          </div>
          <?php
          // 由資料庫中讀取LIMIT所限制的所有記錄，放入變數$result內
          // $result = $pdo->prepare($query_limit_records);
          // $result->execute();  //沒有需要提供給$PDOStatement的參數
          // $arrSearchName = $couponDao->findWithName($searchName);
          foreach($arr2D as $row){ ?>
            <div class="couponList <?php echo ($row['enabled_state'] == 'S') ? 'couponClose' : 'couponOpen'; ?>" id="couponList">
              <div class="row">
                <div class="col my-auto fw-bold"><?php echo $row['coupon_id']; ?></div>
                <div class="col my-auto"><?php echo $row['coupon_name']; ?></div>
                <!--  <img src="此屬性可以是一張圖片的URL或是一個可以送回一張圖片的PHP程式,需要傳入圖片的識別鍵值(即圖片所屬紀錄的Primary Key)"   ...> -->
                <div class="col my-auto">
                    <img class="w-100" src="serverImplement/coupon_Icon.php?searchKey=<?php echo $row['coupon_id']; ?>" alt="" />
                    <!-- <?php echo $image_name; ?> -->
                </div>
                <div class="col my-auto"><?php echo $row['coupon_code']; ?></div>
                <div class="col my-auto"><?php quanumber($row['quantity']); ?></div>
                <div class="col my-auto"><?php typeName($row['coupon_type']); ?></div>
                <div class="col my-auto"><?php useName($row['use_type']); ?></div>
                <div class="col my-auto"><?php echo $row['start_time']; ?></div>
                <div class="col my-auto"><?php echo $row['end_time']; ?></div>

                <!-- 狀態開關 -->
                <?php 
                  $checked = ($row['enabled_state']== 'S' ? '' : 'checked');
                ?>
                <div class="col my-auto" class="form-switch p-0">
                  <label class="switchod">
                    <input class="form-check-input m-auto" name="state" type="checkbox" value="<?php echo $row['enabled_state']; ?>" role="switch" <?php echo $checked; ?>><span class="slider"></span>
                  </label>
                </div>


                <!-- 編輯按鈕 -->
                <div class="col my-auto">
                  <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $row['coupon_code']; ?>" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis"></i>
                  </button>
                </div>
                <div class="collapse" id="<?php echo $row['coupon_code']; ?>">
                  <div class="card card-body editBlock">
                    <button type="button" id="lookAllbtn" class="btn btn-success col editbtn" data-bs-toggle="modal" data-bs-target="#seeMore" disabled>查看</button>
                    <button class="btn btn-secondary col editbtn mx-4"><a href="couponUpdate.php?coupon_id=<?php echo $row['coupon_id'] ?>">編輯</a></button>
                    <button class="btn btn-danger col editbtn" onclick="confirmDelete(<?php echo $row['coupon_id'] ?>)">刪除</button>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>               
        </div>
      </div>
    </div>

    <!-- 換頁功能 -->
    <div class="d-flex justify-content-center align-items-center mt-3">
        <!-- 第一頁 -->
        <div class="mx-2">
          <button class="btn changePagebtn" <?php echo $pageNum > 0 ? "" : "disabled" ?>>
            <a href="<?php echo("$currentPage?pageNum=0$queryString_Recordset1"); ?>"><i class="fa-solid fa-backward-fast fa-xl"></i></a>
          </button> 
        </div>
        <!-- 前一頁 -->
        <div class="mx-2">
          <button class="btn changePagebtn" <?php echo $pageNum > 0 ? "" : "disabled" ?>>    
            <a href="<?php $pm = $pageNum - 1; echo("$currentPage?pageNum=$pm$queryString_Recordset1"); ?>"><i class="fa-solid fa-backward-step fa-xl"></i></a>
          </button>
        </div>
        <!-- 第  頁/共 頁 -->
        <div class="mx-2 pageBox p-3"> 
          <?php 
          $pNo = $pageNum+1; $totPage = $totalPages+1; echo "第  $pNo 頁 / 共 $totPage 頁"; 
          ?>
        </div>
        <!-- 下一頁 -->
        <div class="mx-2">
          <button class="btn changePagebtn" <?php echo $pageNum < $totalPages ? "" : "disabled" ?>>  
            <a href="<?php $pm = $pageNum + 1; echo ("$currentPage?pageNum=$pm$queryString_Recordset1"); ?>"><i class="fa-solid fa-forward-step fa-xl"></i></a>
          </button>
        </div>
        <!-- 最後頁 -->
        <div class="mx-2">
          <button class="btn changePagebtn" <?php echo $pageNum < $totalPages ? "" : "disabled" ?>>  
            <a href="<?php echo ("$currentPage?pageNum=$totalPages$queryString_Recordset1"); ?>"><i class="fa-solid fa-forward-fast fa-xl"></i></a>
          </button>
        </div>
    </div>

    <!-- 顯示執行的結果  -->
    <div id="message">
        <?php
          if (isset($_SESSION['coupon_Message'])) {
            echo '<script>window.onload = function() {Swal.fire("' . $_SESSION['coupon_Message'] . '")};</script>';
            unset($_SESSION['coupon_Message']);
        }
        ?>
    </div>
  </div>
  <!-- 查看彈出視窗 -->
  <div class="modal fade" id="seeMore" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h1 class="modal-title fs-5" id="exampleModalLabel">詳細資料</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  // 彈出刪除
  function confirmDelete(e) {
    Swal.fire({
      title: '確定要刪除?',
      text: "將無法回復資料!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: '刪除'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire(
          location.href = 'serverImplement/couponDelete.php?coupon_id=' + e,
          '刪除!',
          '優惠券已刪除',
          '關閉'
        )
      }
    })
  };



  //每當切換滑桿時，要變更checkbox的value，並傳回資料庫
  // const listTodo = $('#SwitchCheck');  
  $('div').on("change", 'input', function () {
    $x = $(this);
    $coupon = $x.parents('#couponList');
    if(this.checked == true) {
      $x.attr('value','');
      $coupon.css({
              'filter': 'grayscale(0)',
              'opacity': '1'
            });
    }else{
      $x.attr('value','S');
      $coupon.css({
              'filter': 'grayscale(100%)',
              'opacity': '.6'
            });
    };

    const c_value = $(this).attr('value');
    const c_id = $(this).parents('div').children('div:nth-child(1)').text();

    $.ajax({
      url: 'serverImplement/updateEnabledState.php',
      type: 'POST',
      data: {'value':c_value, "id":c_id},
      datatype: 'json'
    })
  });


  //按下編輯底部padding隱藏
  // const edit = $('#edit');
  $('div').on('click', 'button:nth-child(1)', function(){
    $edit = $(this);
    $boxPaddingBottom = $(this).parents('#couponList');
    if( $edit.attr('aria-expanded') == 'true' ){
      $boxPaddingBottom.css('padding', '1.5rem 0 0');
    }else{     
      $boxPaddingBottom.css('padding', '1.5rem 0');
    }
  });


  //點擊查看生成內容
  // const lookAllbtn = $('#lookAllbtn');  
  // $('td').on("click", 'button:nth-child(1)', function () {
  //   const c_id = $(this).parents('tr').children('td:nth-child(1)').text();

  //   $.ajax({
  //     url: 'serverImplement/ShowCouponFlie.php',
  //     type: 'GET',
  //     data: {"id":c_id},
  //     datatype: 'html'
  //   }).done(function(data){

  //     console.log(data);
  //      for(let $i = 0 ; $i < 10 ; $i++){
  //        console.log(data[$i]);
      //  };
        
    // });

</script>
</body>
</html>