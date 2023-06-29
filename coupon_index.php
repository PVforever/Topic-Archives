<?php 
session_start(); 
?>
<?php require_once('conn.php'); ?>
<?php require_once('CRUD_Main_Class.php'); ?>
<?php require_once('coupon_basic.php'); ?>

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


// 算出總共有多少筆商品的SQL敘述，coupon表格的總筆數
// 如果外界有透過GET方法傳入totalRows(表格的總紀錄筆數)
if (isset($_GET['totalRows'])) {
  $totalRows = $_GET['totalRows'];
} else {
	// 否則到資料庫讀取『Book表格的總紀錄筆數』，
	// 放到變數 $totalRows內
	$totalRows  = count($couponDao->findAll()) ;
}
// 計算有幾頁(Page) 0 表示有1頁，1 表示有2頁，
// 例如：有15筆記錄，每頁3筆, 總共5頁($totalPages的值為4)
$totalPages = ceil($totalRows/$maxRows)-1;  // 

$queryString_Recordset1 = "&totalRows=$totalRows";

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

$arr2D = $couponDao->findWithinRange($startRow, $maxRows);

if(!empty($_POST["searchName"])) // 確定是否存在資料
{
  $searchName = $_POST["searchName"];
  $arr2D = $couponDao->findWithName($startRow, $maxRows, $searchName);  // 模糊查詢
}
?>

<!DOCTYPE html >
<html>
<head>
  <meta charset="utf-8" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <title>優惠券管理</title>
  <style>
    a{
      text-decoration: none;
      color: inherit;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <h4 class="mt-5 mb-3 fw-bold">優惠券管理</h4>
    <div class="d-flex justify-content-end text-nowrap pb-3">
      <button class="btn btn-outline-secondary me-auto"><a href="couponInsert.php">新增紀錄</a></button>
      <div class="d-flex">
        <form class="m-0 d-flex" id="search_post">
          <select class="form-select mx-2 w-auto" aria-label="Default select example" required>
            <option selected hidden>使用類型</option>
            <option value="where use_type ='0'">平台通用</option>
            <option value="where use_type ='1'">指定書類</option>
            <option value="where use_type ='2'">指定商品</option>
          </select>
        </form>
        <form class="m-0 d-flex" action="coupon_index.php" method="post">
          <input class="form-control me-2" name="searchName" type="search" placeholder="輸入優惠券名稱" aria-label="Search" value="<?php echo $searchName ?>">
          <input class="btn btn-outline-danger search" type="submit" value="搜尋">
        </form>
      </div>
    </div>
    <table class="table table-dark table-hover text-center">
      <thead>
        <tr>
          <th>編號</th>
          <th>名稱</th>
          <th>圖片</th>
          <th>代碼</th> 
          <!-- <th>折抵金額</th> -->
          <!-- <th>金額限制</th> -->
          <th>數量</th>
          <!-- <th>限領數量</th> -->
          <th>優惠券類型</th>              
          <th>使用類型</th>  
          <!-- <th>疊加使用</th> -->
          <!-- <th>賣家等級</th> -->
          <th>開始時間</th>             
          <th>結束時間</th> 
          <th>開啟/關閉</th> 
          <th>編輯</th>
        </tr>
      </thead>
      <tbody class="align-middle">
        <?php
        // 由資料庫中讀取LIMIT所限制的所有記錄，放入變數$result內
        // $result = $pdo->prepare($query_limit_records);
        // $result->execute();  //沒有需要提供給$PDOStatement的參數
        // $arrSearchName = $couponDao->findWithName($searchName);
        foreach($arr2D as $row){ ?>
          <tr style="height: 120px;">
            <td><?php echo $row['coupon_id']; ?></td>
            <td><?php echo $row['coupon_name']; ?></td>
            <!--  <img src="此屬性可以是一張圖片的URL或是一個可以送回一張圖片的PHP程式,需要傳入圖片的識別鍵值(即圖片所屬紀錄的Primary Key)"   ...> -->
            <td>
                <img src="coupon_Icon.php?searchKey=<?php echo $row['coupon_id']; ?>" alt="" />
                <!-- <?php echo $image_name; ?> -->
            </td>
            <td><?php echo $row['coupon_code']; ?></td>
            <td><?php quanumber($row['quantity']); ?></td>
            <td><?php typeName($row['coupon_type']); ?></td>
            <td><?php useName($row['use_type']); ?></td>
            <td><?php echo $row['start_time']; ?></td>
            <td><?php echo $row['end_time']; ?></td>
            <?php 
              $checked = ($row['enabled_state']== 'S' ? '' : 'checked');
            ?>    
            <td class="form-switch">
              <input class="form-check-input m-auto" name="state" type="checkbox" value="<?php echo $row['enabled_state']; ?>" role="switch" id="SwitchCheck" <?php echo $checked; ?>>
            </td>

            <td>
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#seeMore">查看</button>
              <button class="btn btn-secondary"><a href="couponUpdate.php?coupon_id=<?php echo $row['coupon_id'] ?>">編輯</a></button>
              <button class="btn btn-danger" onclick="confirmDelete(<?php echo $row['coupon_id'] ?>)">刪除</button>
            </td>
          </tr>
        <?php } ?>        
      </tbody>
    </table>

    <!-- 換頁功能 -->
    <div class="d-flex justify-content-center align-items-center">
        <div class="mx-1">
        <!-- 第一頁 -->
          <button class="btn btn-outline-secondary" <?php echo $pageNum > 0 ? "" : "disabled" ?>>
            <a href="<?php echo("$currentPage?pageNum=0$queryString_Recordset1"); ?>">第一頁</a>
          </button> 
        </div>
        <div class="mx-1">
            <!-- 前一頁 -->
          <button class="btn btn-outline-secondary" <?php echo $pageNum > 0 ? "" : "disabled" ?>>    
            <a href="<?php $pm = $pageNum - 1; echo("$currentPage?pageNum=$pm$queryString_Recordset1"); ?>">上一頁</a>
          </button>
        </div>
        <div class="mx-1">
          <!-- 第  頁/共 頁 --> 
          <?php 
          $pNo = $pageNum+1; $totPage = $totalPages+1; echo "第  $pNo 頁 / 共 $totPage 頁"; 
          ?>
        </div>
        <div class="mx-1">
            <!-- 下一頁 -->
          <button class="btn btn-outline-secondary" <?php echo $pageNum < $totalPages ? "" : "disabled" ?>>  
            <a href="<?php $pm = $pageNum + 1; echo ("$currentPage?pageNum=$pm$queryString_Recordset1"); ?>">下一頁</a>
          </button>
        </div>
        <div class="mx-1">
            <!-- 最後頁 -->
          <button class="btn btn-outline-secondary" <?php echo $pageNum < $totalPages ? "" : "disabled" ?>>  
            <a href="<?php echo ("$currentPage?pageNum=$totalPages$queryString_Recordset1"); ?>">最後頁</a>
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
        ...
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
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
          location.href = 'couponDelete.php?coupon_id=' + e,
          '刪除!',
          '優惠券已刪除',
          '關閉'
        )
      }
    })
  };

  //每當切換滑桿時，要變更checkbox的value，並傳回資料庫
  const listTodo = $('#SwitchCheck');  
  $('td').on("change", 'input', function () {
    $x = $(this);

    if(this.checked == true) {
      $x.attr('value','');
    }else{
      $x.attr('value','S');
    };

    const c_value = $(this).attr('value');
    const c_id = $(this).parents('tr').children('td:nth-child(1)').text();

    $.ajax({
      url: 'updateEnabledState.php',
      type: 'POST',
      data: {'value':c_value, "id":c_id},
      datatype: 'json'
    })
  });

</script>
</body>
</html>