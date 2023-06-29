<?php
    //Data Access Object 存取資料庫性質的物件.
    class couponDao {
        private $connString = "mysql:host=localhost; port=3306; dbname=coupondb; charset=utf8";
        private $user = "root";
        private $password = "root";
        private $accessOptions = array(
            PDO::ATTR_EMULATE_PREPARES=>false,
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_PERSISTENT => true
        );

        private $pdo = null;

        public function __construct(){
            $this->pdo = new PDO($this->connString, 
                           $this->user, 
                           $this->password, 
                           $this->accessOptions);
        }

        //重製資料表功能
        public function resetcouponTable(){
            try {
                $drop_table = "DROP TABLE IF EXISTS coupon";
                $create_table = "CREATE TABLE coupon (
                  coupon_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '優惠券ID',
                  coupon_name VARCHAR(30) COMMENT '名稱',
                  coupon_icon BLOB DEFAULT COMMENT '圖片',
                  image_name VARCHAR(64) COMMENT '圖片名稱',
                  coupon_code VARCHAR(64) COMMENT '代碼',
                  amount DECIMAL(10,2) COMMENT '折抵金額；若為浮點數則用*',
                  min_point DECIMAL(10,2) COMMENT '金額限制；0表示無限制',
                  quantity INT COMMENT '數量',
                  per_limit INT COMMENT '每人限領張數；NULL表示無限制(有領取判斷，領取區相關沒有才能再領取)',
                  receive_count INT COMMENT '總領取數量',
                  coupon_type INT(1) COMMENT '優惠券類型 (免運券:0,註冊禮券:1,生日禮券:2,購物禮券:3,平台禮券:4)',
                  use_type int(1) COMMENT '使用類型 (平台通用:0, 指定書類:1, 指定商品:2)',
                  overlay INT(1) COMMENT '疊加使用 (不可 = 0,可 = 1)',
                  level_id INT DEFAULT 0, COMMENT '等級ID',
                  start_time DATETIME COMMENT '開始時間',
                  end_time DATETIME COMMENT '結束時間',
                  enabled_state BOOLEAN NOT NULL COMMENT '開啟狀態'
                )";
                  
                  $n = $this->pdo->exec($drop_table);
                //   echo "DROP TABLE Employee,  n=$n<br>";
                  $n = $this->pdo->exec($create_table);
                //   echo "CREATE TABLE Employee,  n=$n<br>";
           } catch(Exception $ex){
                echo "存取資料庫時發生錯誤，訊息:" . $ex->getMessage() . "<br>";
                echo "苦主:" . $ex->getFile() . "<br>";
                echo "行號:" . $ex->getLine() . "<br>";
                echo "Code:" . $ex->getCode() . "<br>";
                echo "堆疊:" . $ex->getTraceAsString() . "<br>";
           }
        }

        //新增
        public function save($Coupon_basic) {
              $insert =   "INSERT INTO `coupon` (`coupon_id`, `coupon_name`, `coupon_icon`, `image_name`, `coupon_code`, `amount`, `min_point`, `quantity`, `per_limit`, `receive_count`, `coupon_type`, `use_type`, `overlay`, `level_id`, `start_time`, `end_time`, `enabled_state`) VALUES (:id, :name, :icon, :i_name, :code, :amo, :min, :qua, :per, :rec, :c_type, :u_type, :over, :lv, :s_time, :e_time, :e_sta)";

              $pdoStmt = $this->pdo->prepare($insert);
              $pdoStmt->bindValue(":id", NULL);
              $pdoStmt->bindValue(":name", $Coupon_basic->getName(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":icon", $Coupon_basic->getIcon(), PDO::PARAM_LOB);
              $pdoStmt->bindValue(":code",  $Coupon_basic->getCode(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":i_name",  $Coupon_basic->getImage(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":amo",   $Coupon_basic->getAmount(), PDO::PARAM_INT);
              $pdoStmt->bindValue(":min", $Coupon_basic->getMin_point(), PDO::PARAM_INT);
              $pdoStmt->bindValue(":qua", $Coupon_basic->getQuantity(), PDO::PARAM_INT);
              $pdoStmt->bindValue(":per", $Coupon_basic->getPer_limit(), PDO::PARAM_INT);
              $pdoStmt->bindValue(":rec", $Coupon_basic->getReceive_count(), PDO::PARAM_INT);
              $pdoStmt->bindValue(":c_type", $Coupon_basic->getCoupon_type(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":u_type", $Coupon_basic->getUse_type(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":over", $Coupon_basic->getOverlay(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":lv", $Coupon_basic->getLevel_id(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":s_time", $Coupon_basic->getStart_time(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":e_time", $Coupon_basic->getEnd_time(), PDO::PARAM_STR);
              $pdoStmt->bindValue(":e_sta", $Coupon_basic->getEnabled_state(), PDO::PARAM_INT);
              $pdoStmt->execute();
              $num = $pdoStmt->rowCount();
              return $num;
              
        }

        //以id查詢
        public function findById($id) {
          $query = " SELECT * FROM coupon WHERE coupon_id = :id ";
                    
          $pdoStmt = $this->pdo->prepare($query);
              
          $pdoStmt->bindValue(":id", $id, PDO::PARAM_INT);
          $pdoStmt->execute();
          $row = $pdoStmt->fetch(PDO::FETCH_ASSOC);
          return $row;
        }

        // function findBySalaryRange($min, $max) {
        //     $query =   "SELECT * FROM `coupon` c " .
        //                 " WHERE c.salary BETWEEN :min AND :max" ; 

        //     $pdoStmt =  $this->pdo->prepare($query);
            
        //     $pdoStmt->bindValue(":min", $min, PDO::PARAM_INT);
        //     $pdoStmt->bindValue(":max", $max, PDO::PARAM_INT);
        //     $pdoStmt->execute();
        //     $arr2D = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        //     return $arr2D;
        // }
        
        //查尋全部
        public function findAll() {
          $query =   "SELECT * FROM `coupon`"; 

          $pdoStmt =  $this->pdo->prepare($query);
          
          $pdoStmt->execute();
          $arr2D = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
          return $arr2D;
        }

        //變更狀態
        public function updateEnabled_stateById($e_sta,$id) {
          $query = "UPDATE coupon c SET c.enabled_state = :e_sta WHERE c.coupon_id = :id";
          $pdoStmt = $this->pdo->prepare($query);
          $pdoStmt->bindValue(":e_sta", $e_sta, PDO::PARAM_INT);
          $pdoStmt->bindValue(":id", $id, PDO::PARAM_INT);
          $pdoStmt->execute();
          return $pdoStmt->rowCount();
        }

      //範圍查找
      public function findWithinRange($startRow, $maxRow) {
        $query =  " SELECT c.*
                    FROM coupon c  
                    LIMIT :start, :max "; 

        $pdoStmt =  $this->pdo->prepare($query);
        $pdoStmt->bindValue(":start",  $startRow, PDO::PARAM_INT);
        $pdoStmt->bindValue(":max",  $maxRow, PDO::PARAM_INT);
        $pdoStmt->execute();
        $arr2D = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        return $arr2D;
      }

      //範圍查找
      // public function findWithinRange($startRow, $maxRow, $searchName) {
      //   if($searchName === ""){
      //     $query =  " SELECT c.*
      //               FROM coupon c  
      //               LIMIT :start, :max "; 
      //     $pdoStmt =  $this->pdo->prepare($query);
      //     $pdoStmt->bindValue(":start",  $startRow, PDO::PARAM_INT);
      //     $pdoStmt->bindValue(":max",  $maxRow, PDO::PARAM_INT);
      //   }else{
      //     $query =  " SELECT c.*
      //               FROM coupon c
      //               WHERE c.coupon_name LIKE CONCAT('%', :nam, '%')
      //               LIMIT :start, :max";
      //     $pdoStmt =  $this->pdo->prepare($query);
      //     $pdoStmt->bindValue(":nam",  $searchName, PDO::PARAM_STR);
      //     $pdoStmt->bindValue(":start",  $startRow, PDO::PARAM_INT);
      //     $pdoStmt->bindValue(":max",  $maxRow, PDO::PARAM_INT);
      //   }
        
      //   $pdoStmt->execute();
      //   $arr2D = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
      //   return $arr2D;
      // }

      //查尋名稱
      public function findWithName($startRow, $maxRow, $searchName) {
        $query =   " SELECT c.*
                    FROM coupon c
                    WHERE c.coupon_name LIKE CONCAT('%', :nam, '%')
                    LIMIT :start, :max";
        $pdoStmt =  $this->pdo->prepare($query);
        $pdoStmt->bindValue(":nam", $searchName, PDO::PARAM_STR);
        $pdoStmt->bindValue(":start",  $startRow, PDO::PARAM_INT);
        $pdoStmt->bindValue(":max",  $maxRow, PDO::PARAM_INT);
        $pdoStmt->execute();
        $arr2D = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        return $arr2D;
      }

      //查尋使用類型
      public function findUseType($startRow, $maxRow, $useType) {
        $query =   "SELECT * 
                    FROM coupon
                    WHERE use_type LIKE :u_type
                    LIMIT :start, :max";

        $pdoStmt =  $this->pdo->prepare($query);
        $pdoStmt->bindValue(":u_type", $useType, PDO::PARAM_INT);
        $pdoStmt->bindValue(":start",  $startRow, PDO::PARAM_INT);
        $pdoStmt->bindValue(":max",  $maxRow, PDO::PARAM_INT);
        $pdoStmt->execute();
        $arr2D = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        return $arr2D;
      }


      //刪除
      public function deleteById($id){
        $query =   "DELETE FROM `coupon` WHERE coupon_id = :id" ; 

        $pdoStmt =  $this->pdo->prepare($query);
        $pdoStmt->bindValue(":id", $id, PDO::PARAM_INT);
        $pdoStmt->execute();
        $rowCount = $pdoStmt->rowCount();
        return $rowCount;
      }

      //更新
      public function update($Coupon_basic) {
        $insert = "UPDATE `coupon` c SET c.coupon_name = :name, c.coupon_icon = :icon, c.image_name = :i_name, c.coupon_code = :code, c.amount = :amo, c.min_point = :min, c.quantity = :qua, c.per_limit = :per, c.coupon_type = :c_type,  c.use_type = :u_type,  c.overlay = :over, c.level_id = :lv,  c.start_time = :s_time,  c.end_time = :e_time, c.enabled_state = :e_sta WHERE c.coupon_id = :id"; 

            $pdoStmt = $this->pdo->prepare($insert);
            $pdoStmt->bindValue(":name", $Coupon_basic->getName(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":icon", 
            $Coupon_basic->getIcon(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":i_name",  $Coupon_basic->getImage(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":code",  $Coupon_basic->getCode(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":amo",   $Coupon_basic->getAmount(), PDO::PARAM_INT);
            $pdoStmt->bindValue(":min", $Coupon_basic->getMin_point(), PDO::PARAM_INT);
            $pdoStmt->bindValue(":qua", $Coupon_basic->getQuantity(), PDO::PARAM_INT);
            $pdoStmt->bindValue(":per", $Coupon_basic->getPer_limit(), PDO::PARAM_INT);
            $pdoStmt->bindValue(":c_type", $Coupon_basic->getCoupon_type(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":u_type", $Coupon_basic->getUse_type(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":over", $Coupon_basic->getOverlay(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":lv", $Coupon_basic->getLevel_id(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":s_time", $Coupon_basic->getStart_time(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":e_time", $Coupon_basic->getEnd_time(), PDO::PARAM_STR);
            $pdoStmt->bindValue(":e_sta", $Coupon_basic->getEnabled_state(), PDO::PARAM_INT);
            $pdoStmt->bindValue(":id", $Coupon_basic->getId(), PDO::PARAM_INT);
            $pdoStmt->execute();
            $num = $pdoStmt->rowCount();
            return $num;
      }

      public function updateWithoutCoverImage($Coupon_basic) {
        $update =   "UPDATE `coupon` c SET c.coupon_name = :name, c.coupon_code = :code, c.amount = :amo, c.min_point = :min, c.quantity = :qua, c.per_limit = :per, c.coupon_type = :c_type,  c.use_type = :u_type,  c.overlay = :over, c.level_id = :lv,  c.start_time = :s_time,  c.end_time = :e_time, c.enabled_state = :e_sta WHERE c.coupon_id = :id"; 

        $pdoStmt = $this->pdo->prepare($update);
        $pdoStmt->bindValue(":name", $Coupon_basic->getName(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":code",  $Coupon_basic->getCode(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":amo",   $Coupon_basic->getAmount(), PDO::PARAM_INT);
        $pdoStmt->bindValue(":min", $Coupon_basic->getMin_point(), PDO::PARAM_INT);
        $pdoStmt->bindValue(":qua", $Coupon_basic->getQuantity(), PDO::PARAM_INT);
        $pdoStmt->bindValue(":per", $Coupon_basic->getPer_limit(), PDO::PARAM_INT);
        $pdoStmt->bindValue(":c_type", $Coupon_basic->getCoupon_type(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":u_type", $Coupon_basic->getUse_type(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":over", $Coupon_basic->getOverlay(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":lv", $Coupon_basic->getLevel_id(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":s_time", $Coupon_basic->getStart_time(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":e_time", $Coupon_basic->getEnd_time(), PDO::PARAM_STR);
        $pdoStmt->bindValue(":e_sta", $Coupon_basic->getEnabled_state(), PDO::PARAM_INT);
        $pdoStmt->bindValue(":id", $Coupon_basic->getId(), PDO::PARAM_INT);
        $pdoStmt->execute();
        $num = $pdoStmt->rowCount();
        return $num;
      }


    
    }
    ?>
</body>
</html>