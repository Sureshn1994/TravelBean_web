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
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_My_list extends My_list {
  /**
     * To initialize class objects/variables.
     */
  public function __construct()
  {
      parent::__construct();
  }
   /**
     * Used to Check list exist.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $return_arr return where condition.
     */
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

}
