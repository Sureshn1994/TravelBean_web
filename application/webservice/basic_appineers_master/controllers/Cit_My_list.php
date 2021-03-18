<?php
/**
 * Description of Resend Otp Extended Controller
 * 
 * @module Extended Resend Otp
 * 
 * @class Cit_Resend_otp.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Resend_otp.php
 * 
 * @author Suresh Nakate
 * 
 * @date 08.03.2021
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_My_list extends My_list {
  public function __construct()
  {
      parent::__construct();
  }
  public function checkListExist($input_params=array()){
      $return_arr['message']='';
     	$return_arr['status']='1';
    
     	 if(false == empty($input_params['list_id']))
     	 {
            $this->db->from("my_list");
            $this->db->select("iMyListId AS list_id");
            $this->db->where_in("iMyListId", $input_params['list_id']);
            $list_data=$this->db->get()->result_array();
          if(true == empty($list_data)){
             $return_arr['checklistexist']['0']['message']="No List available";
             $return_arr['checklistexist']['0']['status'] = "0";
             return  $return_arr;
          }else{
          	$return_arr['list_id']=$list_data;
          }
      }
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
    
      return $return_arr;
    
  }


  public function checkServiceArea($input_params=array()){

 
        $user_id=$input_params['user_id'];
      
        $return_arr = array();
        $insert_arr = $service_area_id=array();
    
    
          $insert_arr['0']['vSate']=$input_params['state_name'];
          $insert_arr['0']['vCity']=$input_params['city'];
          $insert_arr['0']['vCountry']=$input_params['country'];
          $this->db->select('iServiceAreaId');
          $this->db->from('service_area');
          $this->db->where('vCity', $input_params['city']);
          $this->db->where('vSate', $input_params['state_name']);
          $this->db->where('vCountry', $input_params['country']);
          $data=$this->db->get()->result_array();
         //echo $this->db->last_query();exit;
          if(false == empty($data)){
           unset($insert_arr);
          }
          //print_r($insert_arr);exit;
           if(is_array($insert_arr) && false == empty($insert_arr))
          {

            $this->db->insert_batch("service_area",$insert_arr);
           // echo $this->db->last_query();exit;
          }
 
        $this->db->select('iServiceAreaId');
          $this->db->from('service_area');
          $this->db->where('vCity', $input_params['city']);
          $this->db->where('vSate', $input_params['state_name']);
          $this->db->where('vCountry', $input_params['country']);
          $arrServiceArea=$this->db->get()->result_array();
        $return["service_area_id"]= $arrServiceArea['0']['iServiceAreaId'];
        $return["success"]  = true;
        return $return;
    
  }

}

 

?>
