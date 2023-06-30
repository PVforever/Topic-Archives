<?php
    error_reporting(E_ALL ^ E_DEPRECATED); // 通知PHP顯示除了Deprecated之外的所有訊息 
	session_start(); 
	ob_start();
    require_once('../linkSettings/conn.php'); 
?>
<?php
/* 
   程式的功能：讀取book表格內的圖片欄位。
   當瀏覽器對本程式提出HTTP請求時，瀏覽器會透過GET方法送來要讀取圖片之紀錄的
   bookID，如：
     BookCoverImage.php?searchKey=xxxxx
     
   本程式會讀取book表格內，bookID為xxxxx之記錄的bookID欄與CoverImage欄。讀取後將
   CoverImage欄送回給提出請求的瀏覽器。.
   
*/
$sid = 0 ; 
if (!isset($_GET['searchKey'])) {
    $sid = 1 ;
} else {
    $sid = $_GET['searchKey'];
}
$sql = "SELECT  coupon_id, coupon_icon from coupon where coupon_id = ? " ;  
$pdoStmt = $pdo->prepare($sql);
$pdoStmt->execute(array($sid));
$result = $pdoStmt->fetch(PDO::FETCH_ASSOC);
 
echo  $result['coupon_icon'];  
?>