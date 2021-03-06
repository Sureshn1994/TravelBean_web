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
 * @class My_lists_model.php
 *
 * @path application\webservice\basic_appineers_master\models\My_lists_model.php
 * 
 */

class My_list_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('lib_log');
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * This method is used to execute database queries for my List API.
     * 
     * @param array $params_arr params_arr array to process review block.
     * 
     * @return array $return_arr returns response of review block.
     */
    public function set_my_list($params_arr = array())
    {
        try {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            if (isset($params_arr["list_name"])) {
                $this->db->set("vListName", $params_arr["list_name"]);
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("IUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["address"])) {
                $this->db->set("vAddress", $params_arr["address"]);
            }

            if (isset($params_arr["zip_code"])) {
                $this->db->set("vZipCode", $params_arr["zip_code"]);
            }

            if (isset($params_arr["to_country_code"])) {
                $this->db->set("VToCountryCode", $params_arr["to_country_code"]);
            }
            if (isset($params_arr["to_country_name"])) {
                $this->db->set("vToCountryName", $params_arr["to_country_name"]);
            }
            if (isset($params_arr["to_currency"])) {
                $this->db->set("vToCurrency", $params_arr["to_currency"]);
            }
            if (isset($params_arr["continent"])) {
                $this->db->set("vContinent", $params_arr["continent"]);
            }
            if (isset($params_arr["from_country_code"])) {
                $this->db->set("VFromCountryCode", $params_arr["from_country_code"]);
            }
            if (isset($params_arr["from_country_name"])) {
                $this->db->set("vFromCountryName", $params_arr["from_country_name"]);
            }
            if (isset($params_arr["from_currency"])) {
                $this->db->set("vFromCurrency", $params_arr["from_currency"]);
            }


            $this->db->insert("my_list");
            $insert_id = $this->db->insert_id();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "list_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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



    /**
     * This method is used to execute database queries for getting list .
     * 
     * @param string $review_id review_id is used to process review block.
     * 
     * @return array $return_arr returns response of review block.
     */
    public function  get_list_details($arrResult)
    {

        try {
            $result_arr = array();

            $this->db->start_cache();
            $this->db->from("my_list AS ml");
            $this->db->select("ml.iMyListId AS list_id");
            $this->db->select("ml.vListName AS list_name");
            $this->db->select("ml.vAddress AS address");
            $this->db->select("ml.vZipCode AS zipcode");
            $this->db->select("ml.VFromCountryCode  AS from_country_code");
            $this->db->select("ml.vFromCountryName AS from_country_name");
            $this->db->select("ml.vFromCurrency AS from_currency");
            $this->db->select("ml.vContinent AS from_continent");
            $this->db->select("ml.VToCountryCode AS to_country_code");
            $this->db->select("ml.vToCountryName AS to_country_name");
            $this->db->select("ml.vToCurrency AS to_currency");
            $this->db->select("ml.dtAddedAt AS created_date");
            if (isset($arrResult['user_id']) && $arrResult['user_id'] != "") {
                $this->db->where("ml.IUserId =", $arrResult['user_id']);
            }


            $this->db->stop_cache();

            $this->db->order_by("ml.dtAddedAt", "desc");

            $result_obj = $this->db->get();
            // echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $this->db->flush_cache();
            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        }catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

     /**
     * This method is used to execute database queries for get sum list amount .
     * 
     * @param string $list_id is used to process  block.
     * 
     * @return array $return_arr returns response of block.
     */

    public function get_sum_list_amout($list_id)
    {
        try {

            $strSql = "select SUM(dAmount) as total_list_amout,SUM(dTaxAmout) as total_tax_amout,SUM(dTotalAmout) as total_product_amount from my_list_data where iMyListId ='" . $list_id . "'";
            $result_obj =  $this->db->query($strSql);

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            // echo $this->db->last_query();exit;

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * This method is used to execute database queries for Delet list .
     * 
     * @param string $list_id is used to process  block.
     * 
     * @return array $return_arr returns response of block.
     */

    public function delete_list($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["list_id"])) {
                $this->db->where("iMyListId =", $params_arr["list_id"]);
            }
            $this->db->stop_cache();
            $res = $this->db->delete("my_list");
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
