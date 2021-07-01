<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Go Ad Free Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Go Ad Free
 *
 * @class Go_ad_free.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Go_ad_free.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 27.09.2019
 */

class Go_ad_free extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "update_transaction_data",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('go_ad_free_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * rules_go_ad_free method is used to validate api input params.
     * @created priyanka chillakuru | 26.09.2019
     * @modified priyanka chillakuru | 27.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_go_ad_free($request_arr = array())
    {
         $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),

             "product_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "product_id_required",
                )
            ),

              "receipt_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "receipt_type_required",
                )
            ),
         
           
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "go_ad_free");

        return $valid_res;
    }

    /**
     * start_go_ad_free method is used to initiate api execution flow.
     * @created priyanka chillakuru | 26.09.2019
     * @modified priyanka chillakuru | 27.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_go_ad_free($request_arr = array(), $inner_api = FALSE)
    {
        try
        {
            $validation_res = $this->rules_go_ad_free($request_arr);
            if ($validation_res["success"] == "-5")
            {
                if ($inner_api === TRUE)
                {
                    return $validation_res;
                }
                else
                {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

           // $input_params = $this->update_transaction_data($input_params);
            $input_params = $this->check_user_transaction_exists($input_params);

            $condition_res = $this->check_status($input_params);

            if ($condition_res["success"])
            {

            $input_params = $this->update_transaction_data($input_params);

            $output_response = $this->users_finish_success($input_params);
            return $output_response;

            }else
            {

            $input_params = $this->add_transaction_data($input_params);

            $output_response = $this->users_finish_success($input_params);
            return $output_response;


            }


            
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }


    /**
     * check_user_transaction_exists method is used to process custom function.
     * @created Suresh Nakate | 09.04.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_user_transaction_exists($input_params = array())
    {
        if (!method_exists($this, "checkUserTransactionExit"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkUserTransactionExit($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }


     /**
     * check_status method is used to process conditions.
     * @created priyanka chillakuru | 12.09.2019
     * @modified priyanka chillakuru | 12.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_status($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["status"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }


    /**
     * add_transaction_data method is used to process query block.
     * @created priyanka chillakuru | 26.09.2019
     * @modified priyanka chillakuru | 26.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function add_transaction_data($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
          
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            if (isset($input_params["original_transaction_id"]))
            {
                $params_arr["original_transaction_id"] = $input_params["original_transaction_id"];
            }

            if (isset($input_params["receipt_type"]))
            {
                $params_arr["receipt_type"] = $input_params["receipt_type"];
            }
            if($input_params["receipt_type"]=="ios")
            {

                  $sample_json = file_get_contents($_FILES['receipt_data']['tmp_name']);

                    if (isset($sample_json))
                    {
                        $params_arr["receipt_data_v1"] =$sample_json;
                    }
            }
            else
            {
                if(isset($input_params["purchase_token"])){
                $params_arr["receipt_data_v1"] = $input_params["purchase_token"];
                }

            }

          
            if (isset($input_params["product_id"]))
            {
                $params_arr["product_id"] = $input_params["product_id"];
            }

            if($input_params["product_id"]=='com.appineers.pharos.7days')
            {
              
                    //$expire_date= date('Y-m-d h:i:s',strtotime("+7 day"));
                    $date = $current_date;
                    $date = strtotime($date);
                    $date = strtotime("+3 minute", $date);

                    $ext_date = date('Y-m-d H:i:s', $date);

                    $params_arr["expiry_date_v1"] = $ext_date;
            }
           
            $this->block_result = $this->users_model->add_transaction_data($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["add_transaction_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }




     /**
     * update_transaction_data method is used to process query block.
     * @created priyanka chillakuru | 26.09.2019
     * @modified Suresh Nakate | 26.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_transaction_data($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $current_timezone = date_default_timezone_get();
            // convert the current timezone to UTC
             date_default_timezone_set('UTC');
            $current_date = date("Y-m-d H:i:s");
            // Again coverting into local timezone
            date_default_timezone_set($current_timezone);

            $params_arr = $where_arr = array();
          
            if (isset($input_params["user_id"]))
            {
                $where_arr["user_id"] = $input_params["user_id"];
            }

             if (isset($input_params["original_transaction_id"]))
            {
                $where_arr["original_transaction_id"] = $input_params["original_transaction_id"];
            }

            if (isset($input_params["original_transaction_id"]))
            {
                $params_arr["original_transaction_id"] = $input_params["original_transaction_id"];
            }

            if (isset($input_params["receipt_type"]))
            {
                $params_arr["receipt_type"] = $input_params["receipt_type"];
            }

            if (isset($input_params["receipt_type"]))
            {
                $params_arr["receipt_type"] = $input_params["receipt_type"];
            }

            $sample_json = file_get_contents($_FILES['receipt_data']['tmp_name']);

            if (isset($sample_json))
            {
                $params_arr["receipt_data_v1"] =$sample_json;
            }
            if (isset($input_params["product_id"]))
            {
                $params_arr["product_id"] = $input_params["product_id"];
            }

            if($input_params["product_id"]=='com.appineers.pharos.7days')
            {
              
                 //$expire_date= date('Y-m-d h:i:s',strtotime("+7 day"));
                    $date = $current_date;
                    $date = strtotime($date);
                    $date = strtotime("+3 minute", $date);

                    $ext_date = date('Y-m-d H:i:s', $date);

                    $params_arr["expiry_date_v1"] = $ext_date;

            }
                
           
            $this->block_result = $this->users_model->update_transaction_data($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_transaction_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * users_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 26.09.2019
     * @modified priyanka chillakuru | 27.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "go_ad_free";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
