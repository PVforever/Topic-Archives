<?php
//
//定義常數 全大寫 前面為名稱，後面為值
// define('SYSTEM_NAME','Hold不住購物商城');
// define('SHOPPING_CART_DISPLAY_MAX_ROW', 2);

$hostname_conn = "localhost";
$database_conn = "coupondb";
$username_conn = "root";
$password_conn = "root";
try {
	$pdo = new PDO("mysql:host=localhost; port=3306; dbname=$database_conn; charset=utf8", $username_conn, $password_conn,
			array(PDO::ATTR_EMULATE_PREPARES=>false,
					PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_PERSISTENT => true
			)
	);
}
catch(PDOException $ex){
	echo "存取資料庫時發生錯誤，訊息:" . $ex->getMessage() . "<br>";
	echo "行號:" . $ex->getLine() . "<br>";
	echo "堆疊:" . $ex->getTraceAsString() . "<br>";
}



// function  getCompany($pdo) {
//     $query = "SELECT id, name  FROM  bookcompany";
//     $pdoStmt = $pdo->prepare($query);
//     $pdoStmt->execute();
    
// 	$companyArray = array();

//     while ($aRecord = $pdoStmt->fetch(PDO::FETCH_ASSOC) ) {
// 	    $id = $aRecord['id'];
// 	    $name = $aRecord['name'];
// 		$itemArr = array($id, mb_substr($name, 0, 4,"UTF-8"));
// 		array_push($companyArray, $itemArr);
//     }
// 	return $companyArray;
// }
// function  getSelectTag($pdo, $name) {
//     $tag = "<SELECT name='$name'> ";  // 
//     $allCompany = getCompany($pdo);
//     for ( $n=0; $n< count($allCompany); $n++ ){
//        $id = $allCompany[$n][0];
// 	   $name = $allCompany[$n][1];
// 	   $tag .= "<option value='$id'> $name </option>" ;
//     }
// 	$tag .= "</SELECT>";
// 	return $tag;
// }

// function  setSelectByOptionTag($pdo, $name, $option) {
//     $tag = "<SELECT name='$name'> ";  // 
//     $allCompany = getCompany($pdo);
//     for ( $n=0; $n< count($allCompany); $n++ ){
//        $id = $allCompany[$n][0];
// 	   $name = $allCompany[$n][1];
// 	   if ($option == $id) {
// 	       $tag .= "<option value='$id' selected > $name </option>" ;
// 	   } else {
// 	   	   $tag .= "<option value='$id'> $name </option>" ;
// 	   }
//     }
// 	$tag .= "</SELECT>";
// 	return $tag;
// }
?>