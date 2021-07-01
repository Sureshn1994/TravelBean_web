<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User review Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User review
 *
 * @class My_list_data_model.php
 *
 * @path application\webservice\basic_appineers_master\models\My_list_data_model.php
 *
 * @version 4.4
 *
 * @author Suresh Nakate
 *
 * @since 08.03.2021
 */

class My_list_data_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * set_my_lists method is used to execute database queries for my List API.
     * @created Suresh Nakate
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function set_my_list_data($params_arr = array())
    {
        try
        {

            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            if (isset($params_arr["item_name"]))
            {
                $this->db->set("vItemName", $params_arr["item_name"]);
            }
            if (isset($params_arr["list_id"]))
            {
                $this->db->set("iMyListId", $params_arr["list_id"]);
            }
            if (isset($params_arr["item_amout"]))
            {
                $this->db->set("dAmount", $params_arr["item_amout"]);
            }
            if (isset($params_arr["tax_type"]))
            {
                $this->db->set("vTaxType", $params_arr["tax_type"]);
            } 
            if (isset($params_arr["product_type"]))
            {
                $this->db->set("vProductType", $params_arr["product_type"]);
            } 
            if (isset($params_arr["tax_amout"]))
            {
                $this->db->set("dTaxAmout", $params_arr["tax_amout"]);
            } 
            if (isset($params_arr["total_amout"]))
            {
                $this->db->set("dTotalAmout", $params_arr["total_amout"]);
            } 
           
            
            $this->db->insert("my_list_data");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "list_data_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }



    public function get_list_total($input_params)
    {
        try
        {

            $strSql ="select SUM(dAmount) as total_list_amount,SUM(dTaxAmout) as total_tax_amount,SUM(dTotalAmout) as total_product_amount from my_list_data where iMyListId ='".$input_params['list_id']."'";
            $result_obj =  $this->db->query($strSql);
        
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            //echo $this->db->last_query();exit;
         
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
            }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }




    /**
     * get_list_details method is used to execute database queries for getting list .
     * @created Suresh Nakate | 08.03.2021
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function  get_list_details($arrResult)
    {
        
        try
        {
            $result_arr = array();

            $this->db->start_cache();
            $this->db->from("my_list_data AS mld");
           // $this->db->join("my_list_data AS mld", "mld.iMyListId = ml.iMyListId", "left");

            // $this->db->select("SUM(mld.dAmount) AS total_amount");
             //  $this->db->select("SUM(mld.dTaxAmout) AS total_tax");
            $this->db->select("mld.iMyListDataId AS list_data_id");
            $this->db->select("mld.iMyListId AS list_id");
            
            $this->db->select("mld.vItemName AS item_name");
            $this->db->select("mld.dAmount AS amount");
            $this->db->select("mld.vTaxType  AS tax_type");
            $this->db->select("mld.vProductType AS product_type");
            $this->db->select("mld.dTaxAmout AS tax_amount");
            $this->db->select("mld.dTotalAmout AS total_amount");
             $this->db->select("mld.dtAddedAt AS created_date");
            if (isset($arrResult['list_id']) && $arrResult['list_id'] != "")
            {
                $this->db->where("mld.iMyListId =", $arrResult['list_id']);
            }


            $this->db->stop_cache();

            $this->db->order_by("mld.dtAddedAt", "desc");
           
            $result_obj = $this->db->get();
            //echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $this->db->flush_cache();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
   

    public function delete_data_list($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["list_data_id"]))
            {
                $this->db->where("iMyListDataId =", $params_arr["list_data_id"]);
            }
            $this->db->stop_cache();
            $res = $this->db->delete("my_list_data");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    
}
