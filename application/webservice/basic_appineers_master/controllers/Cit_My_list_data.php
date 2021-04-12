<?php
/**
 * Description of Resend Otp Extended Controller
 * 
 * @module Extended Resend Otp
 * 
 * @class Cit_My_list_data.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_My_list_data.php
 * 
 * @author Suresh Nakate
 * 
 * @date 08.03.2021
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_My_list_data extends My_list_data {
  public function __construct()
  {
      parent::__construct();
  }
  public function checkListExist($input_params=array()){
      $return_arr['message']='';
     	$return_arr['status']='1';
    
     	 if(false == empty($input_params['list_data_id']))
     	 {
            $this->db->from("my_list_data");
            $this->db->select("iMyListDataId  AS list_data_id");
            $this->db->where_in("iMyListDataId ", $input_params['list_data_id']);
            $list_data=$this->db->get()->result_array();
          if(true == empty($list_data)){
             $return_arr['checklistexist']['0']['message']="No List available";
             $return_arr['checklistexist']['0']['status'] = "0";
             return  $return_arr;
          }else{
          	$return_arr['list_data_id']=$list_data;
          }
      }
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
    
      return $return_arr;
    
  }


}

 

?>
