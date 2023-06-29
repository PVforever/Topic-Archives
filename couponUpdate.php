<?php session_start(); ?>
<?php require_once('conn.php'); ?>
<?php require_once('CRUD_Main_Class.php'); ?>
<?php require_once('coupon_basic.php'); ?>


<?php
/*
程式的功能：
修改book表格內的某一筆記錄。某些功能與新增記錄相同。.
 
使用者透過瀏覽器瀏覽書籍資料(index.php)時，他可按下某本書籍
的『書名』超連結，瀏覽器會發出 /BookUpdate.php?bookID=xxxxx的請求。
xxxxx為該本書籍的流水號。
 
這樣的請求為瀏覽器對本程式發出的第一次對請求。此時(本程式第一次執行時)，
本程式會透過  $sid = $_GET['bookID']; 取得因使用者按下『書名』超連結所送
來的書籍流水號xxxxx。接著，以此流水號為依據，到資料庫內讀取對應的書籍記錄。
然後本程式會送回一個含有該筆書籍資料的表單讓使用者修改資料。
 
當使用者修改完畢、按下『Submit』按鈕後，這些修改後的資料會由瀏覽器送回到
本程式(瀏覽器第二次對本程式發出請求)。此時，本程式會檢查使用者輸入的資料，
如果有錯誤，送回原輸入資料與錯誤訊息，交由使用者修改。改完後，使用者再次按
下『Submit』按鈕(瀏覽器第三次...對本程式發出請求)。這些資料會再度送回到本
程式。經過檢查，修改後的資料如果沒有錯誤，程式會將這些資料寫入資料庫。
*/
// 下列變數將會存放要傳送給使用者修改的資料，以及使用者透過瀏覽器傳送回來的資料
// $bookID = $_POST['bookID'];
$coupon_id = "";
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
$errReceive_count = "";
$errCoupon_type = "";
$errUse_type = "";
$errOverlay = "";
$errLevel_id = "";
$errStart_time = "";
$errEnd_time = "";
$errEnabled_state = "";
$errPicture = "";
$errDBMessage = "";
// 此變數表示資料是否正確
$validData = 1;
// ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1"))
// 可用來判斷瀏覽器是第1次對本程式發出HTTP請求，還是第2,3,4...次對
// 本程式發出HTTP請求。
//
// "MM_insert"是本表單內的一個隱藏欄位，瀏覽器是第1次對本程式發出HTTP
// 請求時(注意：此時本程式會在Server端執行)瀏覽器不會送來此欄位，但是
// 當本程式產生回應給瀏覽器時，會送回含有名為 "MM_insert" 的隱藏欄位，
// 如 <input type="hidden" name="MM_update" value="form1" />，
// 因此當瀏覽器第2,3,4...次對本程式發出HTTP請求，就會送出有此欄位。
$couponDao = new couponDao();

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	// 讀取使用者傳來的資料
	$coupon_id = $_POST['coupon_id'];
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
	if ($_FILES["uploadFile"]["error"] == 0)   {
		$imageContent = file_get_contents($_FILES['uploadFile']['tmp_name']);
		//$data = mysql_real_escape_string($imageContent);
		//$data = addslashes($imageContent);
		// $_FILES["uploadFile"]["name"] : 圖片檔的檔名
		$fileName = $_FILES["uploadFile"]["name"];
	}
	
	if ($validData) {
		try {
			if (strlen($fileName) > 0 ) {
				// 使用者挑選新的圖片，所以圖片欄要更新
				$coupon = new Coupon_basic($coupon_id, $coupon_name, $imageContent, $fileName, $coupon_code, $amount, $min_point, $quantity, $per_limit, $receive_count, $coupon_type, $use_type, $overlay, $level_id, $start_time, $end_time, $enabled_state);
				$result = $couponDao->update($coupon);
			} else {
				// 使用者並未挑選新的圖片，所以圖片欄不需要更新
				$coupon = new Coupon_basic($coupon_id, $coupon_name, NULL, NULL, $coupon_code, $amount, $min_point, $quantity, $per_limit, $receive_count, $coupon_type, $use_type, $overlay, $level_id, $start_time, $end_time, $enabled_state);
				$result = $couponDao->updateWithoutCoverImage($coupon);
				
			}

			// 請MySQL執行此 $updateSQL 命命
			if ($result==1) {
				// 取得受前一個命令的執行所影響的紀錄個數
				// 1: 表示更新成功(有1筆紀錄)
				// 0: 表示更新失敗(有0筆紀錄)
				$_SESSION['coupon_Message'] = '優惠券修改成功';
				
			} else {
				$_SESSION['coupon_Message'] = '優惠券未變動';
			}
			header("Cache-Control: no-cache, must-revalidate");
			header('Location: coupon_index.php');
		} catch(PDOException $ex) {
			$errDBMessage = '資料庫錯誤:' . $ex->getMessage() . ", Line= " . $ex->getLine();
		}
	} else {
       // 
	}
} else {
	$sid = 0 ;
	if (!isset($_GET['coupon_id'])) {
		$sid = -1 ;
	} else {
		$sid = $_GET['coupon_id'];
	}
	
	$row = $couponDao->findById($sid);
	
	//print_r($row);
	// list($bookID, $title, $author, $price, $companyID, $image, $bookNo, $coverImage) = $row;
	$coupon_id = $row['coupon_id']; 
	$coupon_name = $row['coupon_name']; 
	$coupon_icon = $row['coupon_icon'];
	$image_name = $row['image_name'];
	$coupon_code = $row['coupon_code'];  
	$amount = $row['amount']; 
	$min_point = $row['min_point']; 
	$quantity = $row['quantity'];
	$per_limit = $row['per_limit'];
	$receive_count = $row['receive_count'];
	$coupon_type = $row['coupon_type'];
	$use_type = $row['use_type'];
	$overlay = $row['overlay'];
	$level_id = $row['level_id'];
	$start_time = $row['start_time'];
	$end_time = $row['end_time'];
	$enabled_state = $row['enabled_state'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>優惠券管理</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<style>
	.img_border{
		height: 130px;
		border: 1px solid #ced4da;
	}
</style>
</head>
<body onload="setFocus()">
	<div class="container">
		<h4 class="fw-bold mt-5">優惠券修改</h4>
		<form class="row text-nowrap py-4" id="form1" name="form1" method="post" action="couponUpdate.php" enctype="multipart/form-data">
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
					<input name="coupon_code" type="text" class="form-control border-3" id="Inputcode" value="<?php echo $coupon_code; ?>">
				</div>
				<div class="text-danger"><?php echo $errCoupon_code; ?></div>
			</div>
			<!-- 折抵金額 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputamo" class="form-label me-2 fw-bold">折抵金額</label>
					<input name="amount" type="number" class="form-control border-3" id="Inputamo" value="<?php echo $amount; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errAmount; ?></div>
			</div>
			<!-- 金額限制 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputmin" class="form-label me-2 fw-bold">金額限制</label>
					<input name="min_point" type="number" class="form-control border-3" id="Inputmin" value="<?php echo $min_point; ?>">
				</div>
				<div class="text-danger text-end mt-2"><?php echo $errMin_point; ?></div>
			</div>
			<!-- 發放數量 -->
			<div class="col-6 mb-5">
				<div class="d-flex align-items-center">
					<label for="Inputqua" class="form-label me-2 fw-bold">發放數量</label>
					<input name="quantity" type="number" class="form-control border-3" id="Inputqua" value="<?php echo $quantity; ?>">
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
						<input style="display: none;" name="per_limit" type="number" class="form-control border-3" id="Inputper" value="<?php echo $per_limit == -1 ? -1 : $per_limit; ?>">
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
					<div class="mb-2 me-2 fw-bold">使用類型</div>
					<div class="btn-group mb-3 w-100" role="group" aria-label="Basic radio toggle button group">
						<input type="radio" class="btn-check" name="use_type" id="alluse" autocomplete="off" value="0" <?php echo $use_type == "0" ? "checked" : "" ?>>
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
						<div class="form-check m-3">
							<input class="form-check-input" type="radio" name="overlay" id="overlayon" value="1" <?php echo $overlay == "1" ? "checked" : "" ?>>
							<label class="form-check-label" for="overlayon">可疊加</label>
							</div>
							<div class="form-check m-3">
							<input class="form-check-input" type="radio" name="overlay" id="overlayout" value="0" <?php echo $overlay == "0" ? "checked" : "" ?>>
							<label class="form-check-label" for="overlayout">不可疊加</label>
						</div>
					</div>
				</div>
				<div class="text-danger text-end mt-2 w-100"><?php echo $errOverlay; ?></div>
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
						<img id="image" src='coupon_Icon.php?searchKey=<?php echo $coupon_id ?>'/>
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
					<div class="text-danger"><?php echo $errStart_time; ?></div>
				</div>
			<!-- 結束時間 -->
				<div class="col mb-5">
					<div class="d-flex align-items-center">
						<label for="Input_e_time" class="form-label me-2 fw-bold">結束時間</label>
						<input name="end_time" type="datetime-local" class="form-control border-3" id="Input_e_time" value="<?php echo $end_time; ?>">
					</div>
					<div class="text-danger"><?php echo $errEnd_time; ?></div>
				</div>
			</div>
			<!-- 操作按鈕 -->
			<div class="d-flex justify-content-end">
				<input name="coupon_id" type="hidden" id="coupon_id" value="<?php echo $coupon_id; ?>" />
				<input class="btn btn-primary" type="button" name="update" value="修改" onclick='couponBook()' />
				<a class="btn btn-outline-danger ms-3" href="coupon_index.php">取消</a>
			</div>
			<div id="insert">
				<?php echo $errDBMessage;   // 顯示錯誤訊息 ?>
				<input type="hidden" name="MM_update" value="form1" />
			</div>
		</form>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script type="text/javascript">
		function setFocus(){
			document.getElementById("Inputname").focus();
		}

		function couponBook() {
			document.forms[0].action="couponUpdate.php" ;
			document.forms[0].method="POST";
			document.forms[0].submit();
		}
	</script>
	<!-- 限領張數js -->
	<script>
		const radiounlimited = document.querySelector('#radiounlimited');
		const radiolimit = document.querySelector('#radiolimit');
		const Inputper = document.getElementById('Inputper');
		const errlimit = document.getElementById('errlimit');
		const Input_icon = document.getElementById('Input_icon');
		const image = document.getElementById('image');
		

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

		Input_icon.onchange = function() {
			image.src = URL.createObjectURL(Input_icon.files[0]);
		};

	</script>
</body>
</html>