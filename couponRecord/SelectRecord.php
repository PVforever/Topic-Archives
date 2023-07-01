<?php
    //Data Access Object 存取資料庫性質的物件.
    class couponRecordDao {
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

        //以id查詢
        public function findByRId($id) {
            $query = " SELECT * FROM coupon_record WHERE coupon_id = :id ";
                      
            $pdoStmt = $this->pdo->prepare($query);
                
            $pdoStmt->bindValue(":id", $id, PDO::PARAM_INT);
            $pdoStmt->execute();
            $row = $pdoStmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
    }
?>