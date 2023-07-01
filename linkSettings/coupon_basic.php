<?php
class Coupon_basic {
    private $coupon_id;
    private $coupon_name;
    private $coupon_icon;
    private $image_name;
    private $coupon_code;
    private $amount;
    private $min_point;
    private $quantity;
    private $per_limit;
    private $receive_count;
    private $coupon_type;
    private $use_type;
    private $overlay;
    private $level_id;
    private $start_time;
    private $end_time;
    private $enabled_state;
    
    public function __construct($coupon_id, $coupon_name, $coupon_icon, $image_name, $coupon_code, $amount, $min_point, $quantity, $per_limit, $receive_count, $coupon_type, $use_type, $overlay, $level_id, $start_time, $end_time, $enabled_state)
    {
        $this->coupon_id =  $coupon_id;
        $this->coupon_name =  $coupon_name;
        $this->coupon_icon =  $coupon_icon;
        $this->image_name =  $image_name;
        $this->coupon_code =  $coupon_code;
        $this->amount =  $amount;
        $this->min_point =  $min_point;
        $this->quantity =  $quantity;
        $this->per_limit =  $per_limit;
        $this->receive_count =  $receive_count;
        $this->coupon_type =  $coupon_type;
        $this->use_type =  $use_type;
        $this->overlay =  $overlay;
        $this->level_id =  $level_id;
        $this->start_time =  $start_time;
        $this->end_time =  $end_time;
        $this->enabled_state =  $enabled_state;
        
    }
    
    public function getId(){
        return $this->coupon_id;
    }   
    public function getName(){
        return $this->coupon_name;
    }
    public function getIcon(){
        return $this->coupon_icon;
    }
    public function getImage(){
        return $this->image_name;
    }
    public function getCode(){
        return $this->coupon_code;
    }
    public function getAmount(){
        return $this->amount;
    }
    public function getMin_point(){
        return $this->min_point;
    }
    public function  getQuantity(){
        return $this->quantity;
    }
    public function  getPer_limit(){
        return $this->per_limit;
    }
    public function  getReceive_count(){
        return $this->receive_count;
    }
    public function  getCoupon_type(){
        return $this->coupon_type;
    }
    public function  getUse_type(){
        return $this->use_type;
    }
    public function  getOverlay(){
        return $this->overlay;
    }
    public function  getLevel_id(){
        return $this->level_id;
    }
    public function  getStart_time(){
        return $this->start_time;
    }
    public function  getEnd_time(){
        return $this->end_time;
    }
    public function  getEnabled_state(){
        return $this->enabled_state;
    }
    //
    // public function info (){
    //     return "coupon_name=$this->coupon_name, min_point=$this->min_point";
    // }
}
?>