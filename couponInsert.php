<?php session_start(); ?>
<?php require_once('conn.php'); ?>
<?php require_once('CRUD_Main_Class.php'); ?>
<?php require_once('coupon_basic.php'); ?>
<?php
/* 
   程式功能：新增一筆記錄到book表格內。.
   
     當瀏覽器第一次對本程式發出請求時，本程式會送回一個空白的表單
   讓使用者輸入資料。使用者輸入資料、按下『Submit』按鈕後，這些資
   料會再度送回到本程式(瀏覽器第二次對本程式發出請求)。
     此時，本程式會檢查使用者輸入的資料，如果有錯誤，送回原輸入資
   料與錯誤訊息，交由使用者修改。改完後，使用者再次按下『Submit』
   按鈕。這些資料會再度送回到本程式並進行新一輪的檢查。如果沒有錯
   誤，本程式會將這些資料寫入資料庫。
*/
$coupon_name = "";
$coupon_icon = "";
$image_name = "";
$coupon_code = "";
$amount = "";
$min_point = "";
$quantity = "" ;
$per_limit = "";
$receive_count = "";
$coupon_type = "";
$use_type = "";
$overlay = "";
$level_id = "";
$start_time = "";
$end_time = "";
$enabled_state = "";
//----------------
// 通常使用者輸入之資料必須經過程式的檢查，正確無誤的資料才會寫入
// 資料庫。如果輸入資料有錯誤，將送回錯誤訊息通知使用者修改。
// 下列個變數將存放要送回給使用者看的錯誤訊息。
$errCoupon_name = "";
$errCoupon_code = "";
$errAmount = "";
$errMin_point = "";
$errQuantity = "";
$errPer_limit = "";
$errCoupon_type = "";
$errUse_type = "";
$errOverlay = "";
$errLevel_id = "";
$errStart_time = "";
$errEnd_time = "";
$errPicture = "";
$errDBMessage = "";
$ok_insert = '';

// 此變數表示使用者輸入之資料是否正確無誤，預設值為1，表示正確無誤
$validData = 1;   

// ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1"))
// 可用來判斷瀏覽器是第1次對本程式發出HTTP請求，還是第2,3,4...次對
// 本程式發出HTTP請求。
// 
// "MM_insert"是本表單內的一個隱藏欄位，瀏覽器是第1次對本程式發出HTTP
// 請求時(注意：此時本程式會在Server端執行)瀏覽器不會送來此欄位，但是
// 當本程式產生回應給瀏覽器時，會送回含有名為 "MM_insert" 的隱藏欄位，
// 如 <input type="hidden" name="MM_insert" value="form1" />，
// 因此當瀏覽器第2,3,4...次對本程式發出HTTP請求，就會送出有此欄位。
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	// 表示使用者應該已經輸入了資料，接下來讀取這些輸入資料
	$coupon_name = $_POST['coupon_name'];
	$coupon_code = $_POST['coupon_code'];
	$amount = $_POST['amount'];
	$min_point = $_POST['min_point'];
	$quantity = $_POST['quantity'];
	$per_limit = $_POST['per_limit'];
	$coupon_type = $_POST['coupon_type'];
	$use_type = $_POST['use_type'];
	$overlay = $_POST['overlay'];
	$level_id = $_POST['level_id'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	// 開始檢查輸入資料
	if (empty($coupon_name)) {  
      $errCoupon_name = '*請輸入優惠券名稱';
      $validData = 0;
	}
	if (empty($coupon_code)) {
		$errCoupon_code = '*請輸入代碼';
		$validData = 0;
	}
	if (empty($amount)) {
		$errAmount = '*請填入折抵金額';
		$validData = 0;
	}
	if (empty($min_point)) {
		$errMin_point = '*請填入金額限制';
	} else if (!is_numeric($min_point)) {   // is_int() : 檢查是否為整數
		$errMin_point = '*必須為整數';
		$validData = 0;
	}
	if (empty($quantity)) {
		$errQuantity = '*請填入數量';
		$validData = 0;
	}
	if (empty($per_limit)) {
		$errPer_limit = '*請填入限領張數';
		$validData = 0;
	}
	if (empty($coupon_type)) {
		$errCoupon_type = '*請選擇優惠券類型';
		$validData = 0;
	}
	if (is_null($use_type) || $use_type === '') {
		$errUse_type = '*請選擇使用類型';
		$validData = 0;
	}
	if (is_null($overlay) || $overlay === '') {
		$errOverlay = '*請選擇是否疊加';
		$validData = 0;
	}
	if (empty($level_id)) {
		$errLevel_id = '*請選擇會員領取層級';
		$validData = 0;
	}
	if (empty($start_time)) {
		$errStart_time = '*請選擇開始時間';
		$validData = 0;
	}
	if (empty($end_time)) {
		$errEnd_time = '*請選擇結束時間';
		$validData = 0;
	}
	// $_FILES["uploadFile"]["error"]錯誤代號的含義：
	// http://php.net/manual/en/features.file-upload.errors.php
	if ($_FILES["uploadFile"]["error"] > 0)   {  // 表示上傳資料出問題
		$validData = 0;		
		if  ($_FILES["uploadFile"]["error"] == 4)  {
        	$errPicture = '*未挑選圖片檔';
		}  else {
			$errPicture = '檔案上傳失敗,' . $_FILES["uploadFile"]["error"];
		}
    } else  {  //上傳檔案沒有問題    	
        // file_get_contents():讀取圖片檔案的全部內容，然後以字串的形式傳回該內容。
        $imageContent = file_get_contents($_FILES['uploadFile']['tmp_name']);
		
         
		// addslashes($imageContent):將字串$imageContent內的一些特殊字元
		// (例如', ", ) ... 等 )加以編碼，以免讓資料庫管理系統(DBMS)誤判程式的
		// 送出的 SQL命令
		//$data = mysql_real_escape_string($imageContent);  // 不要用此敘述
		//$data = addslashes($imageContent);
		// $_FILES["uploadFile"]["name"] : 圖片檔的檔名
		$fileName = $_FILES["uploadFile"]["name"];    
    }
    // 如果輸入的資料都正確
	if ($validData) { 
		try {
		// $insertSQL = "Insert Into book (bookID, title,  author,  price, companyID, image, BookNo ,CoverImage) values " .
		// " (null, ?, ?, ?, ?, ?, ?, ?) ";
    //     // 選擇要存取的資料庫
		// $pdoStmt = $pdo->prepare($insertSQL);
		// $pdoStmt->bindValue(1, $title, PDO::PARAM_STR);
		// $pdoStmt->bindValue(2, $author, PDO::PARAM_STR);
		// $pdoStmt->bindValue(3, $price, PDO::PARAM_STR);
		// $pdoStmt->bindValue(4, $companyID, PDO::PARAM_INT);
		// $pdoStmt->bindValue(5, $fileName, PDO::PARAM_STR);
		// $pdoStmt->bindValue(6, $bookNo, PDO::PARAM_STR);
		// $pdoStmt->bindValue(7, $imageContent, PDO::PARAM_LOB);
		
		// $pdoStmt->execute();

    //     // 請MySQL執行此 $insertSQL 命命 
		// $result = $pdoStmt->rowCount();
      $coupon = new Coupon_basic(NULL, $coupon_name, $imageContent ,$fileName, $coupon_code, $amount, $min_point, $quantity, $per_limit, $receive_count, $coupon_type, $use_type, $overlay, $level_id, $start_time, $end_time, $enabled_state);

      $couponDao = new couponDao();
      $result = $couponDao->save($coupon);
        if ($result==1) {
            // $pdoStmt->rowCount(); 取得執行先前之SQL命令所影響的紀錄個數
            // 1: 表示新增成功(有1筆紀錄)
            // 0: 表示新增失敗(有0筆紀錄)
            $_SESSION['coupon_Message'] = '新增成功';
            header('Location: coupon_index.php');   
            //   header('Location: BookList.php?pageNum_Recordset1=' . . '&totalRows_Recordset1=');
        } 
		} catch(PDOException $ex){
            
	            if ( strpos($ex->getMessage(), 'coupon_name') != false ) {
                   $errCoupon_name = '優惠券已存在'; //此錯誤經常會出現，要單獨處理
	            } else {
         		  // 此為存取資料庫時發生其它的錯誤，如網路未開，....
		           $errDBMessage = '資料庫錯誤:' . $ex->getMessage() . ", Line= " . $ex->getLine() ;

	            }
            
        }
	}
} 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>優惠券新增</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<style>
	.img_border{
		height: 130px;
		border: 1px solid #ced4da;
	}
</style>
</head>
<body onload="setFocus('Inputname')">
  <div class="container">
    <h4 class="fw-bold mt-5">優惠券新增</h4>
<!-- 上傳檔案時<form>標籤的 enctype屬性必須是 "multipart/form-data" -->
    <form class="row text-nowrap py-4" id="form1" name="form1" method="post" action="couponInsert.php"  enctype="multipart/form-data" >
      		<!-- 名稱 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputname" class="form-label me-2 fw-bold">名稱</label>
					<input name="coupon_name" type="text" class="border-3 form-control" id="Inputname" value="<?php echo $coupon_name; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errCoupon_name; ?></div>
			</div>
			<!-- 代碼 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputcode" class="form-label me-2 fw-bold">代碼</label>
					<input name="coupon_code" type="text" class="border-3 form-control" id="Inputcode" value="<?php echo $coupon_code; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errCoupon_code; ?></div>
			</div>
			<!-- 折抵金額 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputamo" class="form-label me-2 fw-bold">折抵金額</label>
					<input name="amount" type="number" class="border-3 form-control" id="Inputamo" value="<?php echo $amount; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errAmount; ?></div>
			</div>
			<!-- 金額限制 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputmin" class="form-label me-2 fw-bold">金額限制</label>
					<input name="min_point" type="number" class="border-3 form-control" id="Inputmin" value="<?php echo $min_point; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errMin_point; ?></div>
			</div>
			<!-- 發放數量 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputqua" class="form-label me-2 fw-bold">發放數量</label>
					<input name="quantity" type="number" class="border-3 form-control" id="Inputqua" value="<?php echo $quantity; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errQuantity; ?></div>
			</div>
			<!-- 限領張數 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label class="form-label me-2 fw-bold">限領張數</label>
					<!-- <input name="per_limit" type="checkbox" class="form-control" id="Inputper"> -->
					<div class="d-flex w-100 align-items-center">
						<div>
							<input name="limit" class="form-check-input" type="radio" id="radiounlimited" <?php echo $per_limit == -1 ? "checked" : "" ?>>
							<label class="form-check-label" for="radiounlimited">無限制</label>
						</div>
						<div class="ms-2">
							<input name="limit" class="form-check-input" type="radio" id="radiolimit" <?php echo $per_limit == -1 ? "" : "checked" ?>>
							<label class="form-check-label" for="radiolimit">輸入數量</label>
						</div>
						<input style="display: none;" name="per_limit" type="number" class="form-control w-100 ms-2 border-3" id="Inputper" value="<?php echo $per_limit == -1 ? -1 : $per_limit; ?>">
					</div>
				</div>
				<div id="errlimit" class="text-danger text-end mt-2"><?php echo $errPer_limit; ?></div>
			</div>
			<!-- 優惠券類型 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Input_c_type" class="form-label me-2 fw-bold">優惠券類型</label>
					<select class="form-select border-3" id="Input_c_type" name="coupon_type" required>
						<option value="0" selected>請選擇</option>
						<option value="1" <?php echo $coupon_type == "1" ? "selected" : "" ?>>免運券</option>
						<option value="2" <?php echo $coupon_type == "2" ? "selected" : "" ?>>註冊禮券</option>
						<option value="3" <?php echo $coupon_type == "3" ? "selected" : "" ?>>生日禮券</option>
						<option value="4" <?php echo $coupon_type == "4" ? "selected" : "" ?>>購物禮券</option>
						<option value="5" <?php echo $coupon_type == "5" ? "selected" : "" ?>>平台禮券</option>
					</select>
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errCoupon_type; ?></div>
			</div>
			<!-- 使用類型 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<div class="mb-3 me-2 fw-bold">使用類型</div>
					<div class="btn-group mb-3 w-100" role="group" aria-label="Basic radio toggle button group">
						<input type="radio" class="btn-check" name="use_type" id="alluse" autocomplete="off" value="0" checked>
						<label class="btn btn-outline-primary" for="alluse">平台通用</label>

						<input type="radio" class="btn-check" name="use_type" id="classuse" autocomplete="off" value="1" <?php echo $use_type == "1" ? "checked" : "" ?>>
						<label class="btn btn-outline-primary" for="classuse">指定書類</label>

						<input type="radio" class="btn-check" name="use_type" id="bookuse" autocomplete="off" value="2" <?php echo $use_type == "2" ? "checked" : "" ?>>
						<label class="btn btn-outline-primary" for="bookuse">指定商品</label>
					</div>
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errUse_type; ?></div>
			</div>
			<!-- 疊加使用 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<div class="me-3 fw-bold">疊加使用</div>
					<div class="d-flex">
						<div class="form-check m-1">
							<input class="form-check-input" type="radio" name="overlay" id="overlayon" value="1" <?php echo $overlay == "1" ? "checked" : "" ?>>
							<label class="form-check-label" for="overlayon">可疊加</label>
						</div>
						<div class="form-check m-1">
							<input class="form-check-input" type="radio" name="overlay" id="overlayout" value="0" checked>
							<label class="form-check-label" for="overlayout">不可疊加</label>
						</div>
					</div>
					<div class="text-danger text-end mt-2 w-100"><?php echo $errOverlay; ?></div>
				</div>
			</div>
			<!-- 會員等級 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="memberlv" class="form-label me-2 fw-bold">會員等級</label>
					<select class="form-select border-3" id="memberlv" aria-label="Default select example" name="level_id" required>
						<option value="0" selected>請選擇</option>
						<option value="1" <?php echo $level_id == "1" ? "selected" : "" ?>>無限制</option>
						<option value="2" <?php echo $level_id == "2" ? "selected" : "" ?>>高級會員</option>
					</select>
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errLevel_id; ?></div>
			</div>	
			<!-- 圖片 -->
			<div class="col-6 mb-5">
				<div class="d-flex w-100">
					<label for="Input_icon" class="form-label me-2 fw-bold">圖片</label>
					<div class="img_border border-3 rounded-3 w-100">
						<img src="" id="image">
						<input name="uploadFile" type="file" class="form-control w-auto" id="Input_icon">
					</div>
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errPicture; ?></div>
			</div>
			<!-- 開始時間 -->
			<div class="col-6">
				<div class="col mb-5">
					<div class="d-flex align-items-center">
						<label for="Input_s_time" class="form-label me-2 fw-bold">開始時間</label>
						<input name="start_time" type="datetime-local" class="form-control border-3" id="Input_s_time" value="<?php echo $start_time; ?>">
					</div>
					<div class="text-danger text-end mt-2"><?php echo $errStart_time; ?></div>
				</div>
			<!-- 結束時間 -->
				<div class="col mb-5">
					<div class="d-flex align-items-center">
						<label for="Input_e_time" class="form-label me-2 fw-bold">結束時間</label>
						<input name="end_time" type="datetime-local" class="form-control border-3" id="Input_e_time" value="<?php echo $end_time; ?>">
					</div>
					<div class="text-danger text-end mt-2"><?php echo $errEnd_time; ?></div>
				</div>
			</div>
      <!-- 操作按鈕 -->
			<div class="d-flex justify-content-end">
        		<span class="text-danger"><?php echo $errDBMessage; ?></span>
				<input class="btn btn-primary" type="submit" name="Submit" value="新增" />
				<a class="btn btn-outline-danger ms-3" href="coupon_index.php">取消</a>
			</div>
      <div id="insert">
        <?php echo $errDBMessage; // 顯示錯誤訊息 ?>
        <input type="hidden" name="MM_insert" value="form1" />
      </div>
    </form>
  </div>
<script type="text/javascript">
  function setFocus(fld) {
      document.getElementById(fld).focus();
  }
</script>
<!-- 限領張數js -->
<script>
	const radiounlimited = document.querySelector('#radiounlimited');
	const radiolimit = document.querySelector('#radiolimit');
	const Inputper = document.getElementById('Inputper');
	const errlimit = document.getElementById('errlimit');
	const Input_icon = document.getElementById('Input_icon');

	if (radiounlimited.checked) {
		Inputper.style.display = 'none';
	} else if (radiolimit.checked) {
		Inputper.style.display = 'block';
	}

	radiolimit.addEventListener('change', function() {
		if (this.checked) {
			Inputper.style.display = 'block';
			Inputper.value = "";
			errlimit.style.display = 'block';

		} else {
			Inputper.style.display = 'none';
			errlimit.style.display = 'none';
		}
	});

	radiounlimited.addEventListener('change', function() {
		if (this.checked) {
			Inputper.style.display = 'none';
			errlimit.style.display = 'none';
			Inputper.value = -1;
		} else {
			Inputper.style.display = 'block';
			errlimit.style.display = 'block';
		}
	});
</script>
<script>
	const image = document.getElementById('image');
	Input_icon.onchange = function() {
		image.src = URL.createObjectURL(Input_icon.files[0]);
	};
</script>
</body>
</html>