<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Service Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers

 *
 * @module My List Data
 *
 * @class My_list_data.php
 *
 * @path application\webservice\basic_appineers_master\controllers\My_list_data.php
 * 
 */

class My_list_data extends Cit_Controller
{

    /** @var array $settings_params contains Setting parameters */
    public $settings_params;
    /** @var array $output_params contains output parameters */
    public $output_params;

    /** @var array $single_keys contains single array */
    public $single_keys;

    /** @var array $multiple_keys contains multiple array */
    public $multiple_keys;

    /** @var array $block_result contains query returns result array*/
    public $block_result;

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "set_my_lists_data",
            "get_my_lists_data",
        );
        $this->block_result = array();
        $this->load->library('lib_log');
        $this->load->library('wsresponse');
        $this->load->model('my_list_data_model');
    }


    /**
     * This method is used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_my_list_data($request_arr = array(), $inner_api = FALSE)
    {


        // get the HTTP method, path and body of the request

        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
            case 'GET':
                $output_response =  $this->get_my_list_data($request_arr);
                return  $output_response;
                break;

            case 'POST':
                $output_response =  $this->add_my_list_data($request_arr);
                return  $output_response;
                break;
            case 'DELETE':
                $output_response =  $this->delete_my_list_data($request_arr);
                return  $output_response;
                break;
        }
    }





    /**
     * thsi method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_add_my_list_data($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),

            "item_name" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "item_name_required",
                )
            ),

            "list_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "list_id_required",
                )
            ),

        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_my_list");

        return $valid_res;
    }

    /**
     * This method is used to add my list data input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function add_my_list_data($input_params)
    {

        try {

            $validation_res = $this->rules_add_my_list_data($input_params);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }

            $output_response = array();

            $output_array = $func_array = array();

            $input_params = $this->set_my_list_data($input_params);

            $condition_res = $this->is_posted($input_params);

            if ($condition_res["success"]) {
                $output_response = $this->user_service_finish_success($input_params);

                return $output_response;
            } else {
                $output_response = $this->user_service_finish_success_1($input_params);
                return $output_response;
            }
        } catch (Exception $e) {

            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }

        return $output_response;
    }

    /**
     * This method is used to process store my list.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function set_my_list_data($input_params = array())
    {
        $this->block_result = array();
        try {

            $params_arr = array();

            if (isset($input_params["added_date"])) {
                $params_arr["_dtaddedat"] = $input_params["added_date"];
            } else {
                $params_arr["_dtaddedat"] = "NOW()";
            }
            if (isset($input_params["list_id"])) {
                $params_arr["list_id"] = $input_params["list_id"];
            }
            if (isset($input_params["item_name"])) {
                $params_arr["item_name"] = $input_params["item_name"];
            }
            if (isset($input_params["item_amout"])) {
                $params_arr["item_amout"] = $input_params["item_amout"];
            }
            if (isset($input_params["tax_type"])) {
                $params_arr["tax_type"] = $input_params["tax_type"];
            }
            if (isset($input_params["product_type"])) {
                $params_arr["product_type"] = $input_params["product_type"];
            }
            if (isset($input_params["tax_amout"])) {
                $params_arr["tax_amout"] = $input_params["tax_amout"];
            }
            if (isset($input_params["total_amout"])) {
                $params_arr["total_amout"] = $input_params["total_amout"];
            }

            $this->block_result = $this->my_list_data_model->set_my_list_data($params_arr);

            //$this->block_result = $this->my_list_model_data->set_my_list_data($params_arr);

            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["set_my_list_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This  method is used to process conditions.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_posted($input_params = array())
    {

        $this->block_result = array();
        try {
            $cc_lo_0 = (is_array($input_params["list_data_id"])) ? count($input_params["list_data_id"]) : $input_params["list_data_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("List data is not created.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;

        return $this->block_result;
    }

    /**
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_fetched($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["list_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }


    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function user_service_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "My Lists data added successfully";
        // $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "add_my_list_data");

        return $responce_arr;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function user_service_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "user_service_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_my_list_data";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


    /**
     * This method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_services($request_arr = array())
    {

        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),

            "list_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "list_id_required",
                )
            ),

        );


        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_my_list_data");

        return $valid_res;
    }

    /**
     * This method is used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function get_my_list_data($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_get_services($request_arr);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->get_list_total($input_params);
            $input_params = $this->get_all_list($input_params);

            $condition_res = $this->is_posted($input_params);

            if ($condition_res["success"]) {

                $output_response = $this->get_list_finish_success($input_params);
                return $output_response;
            } else {

                $output_response = $this->get_list_finish_success_1($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        return $output_response;
    }


    /**
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function check_List_exist($input_params = array())
    {


        if (!method_exists($this, "checkListExist")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkListExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkListExist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to process get list total.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_list_total($input_params = array())
    {

        $this->block_result = array();
        try {
            $this->block_result = $this->my_list_data_model->get_list_total($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $input_params["get_all_list"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
     
        return $input_params;
    }



    /**
     * This method is used to process review block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_list($input_params = array())
    {

        $this->block_result = array();
        try {
            $this->block_result = $this->my_list_data_model->get_list_details($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
        }catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $input_params["get_all_list"] = $this->block_result["data"];

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    
    /**
     * This method is used to Delete my list data.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_my_list_data($request_arr = array())
    {
        try {
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $request_arr;

            $condition_res = $this->check_list_exist($input_params);



            if ($condition_res["checkListExist"]["status"]) {
                $input_params = $this->delete_data_list($input_params);

                $output_response = $this->delete_list_finish_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->delete_list_finish_success_1($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        return $output_response;
    }



    /**
     * This method is used to process review block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_data_list($input_params = array())
    {

        $this->block_result = array();
        try {
            $arrResult = array();

            $arrResult['list_data_id']  = isset($input_params["list_data_id"]) ? $input_params["list_data_id"] : "";
            $this->block_result = $this->my_list_data_model->delete_data_list($arrResult);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

            $this->block_result["data"] = $result_arr;
        }catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $input_params["delete_list"] = $this->block_result["data"];

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_list_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "delete_list_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_data_list";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_list_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "delete_list_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_data_list";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */

    public function get_list_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "get_list_finish_success",
            "total_list_amount" => $input_params["total_list_amount"],
            "total_list_tax_amount" => $input_params["total_tax_amount"],
            "total_list_product_amount" => $input_params["total_product_amount"],
        );
        $output_fields = array(
            "list_data_id",
            "list_id",
            "item_name",
            "amount",
            "tax_type",
            "product_type",
            "tax_amount",
            "total_amount",
            "created_date",
        );
        $output_keys = array(
            'get_all_list',
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_my_data_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);


        return $responce_arr;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function get_list_finish_success_1($input_params = array(), $result_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_list_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_my_data_list";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
