<?php

   
/**
 * Description of User Sign Up Email Extended Controller
 * 
 * @module Extended User Sign Up Email
 * 
 * @class Go_ad_free.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Go_ad_free.php
 * 
 * @author CIT Dev Team
 * 
 * @date 10.02.2020
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Go_ad_free extends Go_ad_free {
        public function __construct()
{
    parent::__construct();
}
public function checkUserTransactionExit($input_params=array()){

  
   $user_id=$input_params['user_id'];
   $original_transaction_id=$input_params["original_transaction_id"];
  
   $return_arr['status']='0';



    if(!empty($user_id) && !empty($original_transaction_id)){
        $this->db->select('iUserId,vProductId');
        $this->db->from('user_subscription');
        $this->db->where('iUserId',$user_id);
          $this->db->where('vProductId',$original_transaction_id);
        $transaction_data=$this->db->get()->result_array();
       
        if(!empty($transaction_data)){
           $return_arr['status'] = "1";
           return  $return_arr;
        }
    }
  
  
   
   return  $return_arr; 
}

}
