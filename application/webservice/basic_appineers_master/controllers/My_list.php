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
 * @module Set store review
 *
 * @class set_store_review.php
 *
 * @path application\webservice\basic_appineers_master\controllers\My_lists.php
 *
 * @version 4.4
 *
 * @author Suresh Nakate
 *
 * @since 08.03.2021
 */

class My_list extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
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
            "set_my_lists",
            "get_my_lists",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('my_list_model');
        
    }


    /**
     * start_services method is used to initiate api execution flow.
     * @created Suresh Nakate | 08.03.2021
    
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_my_list($request_arr = array(), $inner_api = FALSE)
    {
        // get the HTTP method, path and body of the request
        
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
          case 'GET':
             $output_response =  $this->get_my_list($request_arr);     
             return  $output_response;
             break;
         
          case 'POST':
           $output_response =  $this->add_my_list($request_arr);
           return  $output_response;
             break;
          case 'DELETE':
           $output_response =  $this->delete_my_list($request_arr);
           return  $output_response;
             break;
        }
    }
	



    /**
     * rules_set_store_review method is used to validate api input params.
     * @created Suresh Nakate | 08.03.2021
    
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_add_service($request_arr = array())
    {       
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
           
            );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_my_list");

        return $valid_res;
    }

    public function add_my_list($input_params){

        try
        {
        
            $validation_res = $this->rules_add_my_list($input_params);
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
           
            $output_array = $func_array = array();

            $input_params = $this->set_my_list($input_params);

            $condition_res = $this->is_posted($input_params);

            if ($condition_res["success"])
            {
            $output_response = $this->user_service_finish_success($input_params);
    
            return $output_response;
            }
            else
            {
                $output_response = $this->user_service_finish_success_1($input_params);
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
     * set_my_lists method is used to process store my list .
     * @created Suresh Nakate
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function set_my_list($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            if (isset($input_params["added_date"]))
            {
                $params_arr["_dtaddedat"] = $input_params["added_date"];
            }else{
               $params_arr["_dtaddedat"] = "NOW()"; 
            }
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["list_name"]))
            {
                $params_arr["list_name"] = $input_params["list_name"];
            }
            if (isset($input_params["address"]))
            {
                $params_arr["address"] = $input_params["address"];
            }
            if (isset($input_params["to_country_code"]))
            {
                $params_arr["to_country_code"] = $input_params["to_country_code"];
            }
            if (isset($input_params["to_country_name"]))
            {
                $params_arr["to_country_name"] = $input_params["to_country_name"];
            }
            if (isset($input_params["to_currency"]))
            {
                $params_arr["to_currency"] = $input_params["to_currency"];
            }
            if (isset($input_params["continent"]))
            {
                $params_arr["continent"] = $input_params["continent"];
            }
            if (isset($input_params["from_country_code"]))
            {
                $params_arr["from_country_code"] = $input_params["from_country_code"];
            }
            if (isset($input_params["from_country_name"]))
            {
                $params_arr["from_country_name"] = $input_params["from_country_name"];
            }
            if (isset($input_params["from_currency"]))
            {
                $params_arr["from_currency"] = $input_params["from_currency"];
            }
      
            $this->block_result = $this->my_list_model->set_my_list($params_arr);

            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
            }
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["set_my_list"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * is_posted method is used to process conditions.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_posted($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $cc_lo_0 = (is_array($input_params["list_id"])) ? count($input_params["list_id"]):$input_params["list_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
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
     * is_posted method is used to process conditions.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_fetched($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $cc_lo_0 = $input_params["list_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
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
     * user_review_finish_success method is used to process finish flow.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_service_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "My Lists added successfully";
        // $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "add_my_list");

        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process loop flow.
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

        $func_array["function"]["name"] = "add_my_list";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


     /**
     * rules_get_services method is used to validate api input params.
     * @created Suresh Nakate | 08.03.2021
     * @param array $request_arr request_arr array is used for api input.
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
            
            );
        
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_my_list");

        return $valid_res;
    }

    /**
     * get my list method is used to initiate api execution flow.
     * @created Suresh Nakate | 08.03.2021
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function get_my_list($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_services($request_arr);
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
            $result_params = $this->get_all_list($input_params);
             
            $condition_res = $this->is_posted($result_params);

            if ($condition_res["success"])
            {
              
               
                $output_response = $this->get_list_finish_success($result_params);
                return $output_response;
            }

            else
            {
 
                $output_response = $this->get_list_finish_success_1($result_params);
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
     * checkuniqueusername method is used to process custom function.
     * @created Suresh Nakate | 08.03.2021 
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_List_exist($input_params = array())
    {
        

        if (!method_exists($this, "checkListExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkListExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkListExist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        //print_r($input_params);
        return $input_params;
    }

    /**
     * get_all_list method is used to process review block.
     * @created SUresh Nakate | 08.03.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_list($input_params = array())
    {
       
        $this->block_result = array();
        try
        {
            $this->block_result = $this->my_list_model->get_list_details($input_params);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
                   
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_all_list"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        //print_r($input_params);exit;
       return $input_params;
    }


    public function delete_my_list($request_arr = array())
    {
      try
        {
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $request_arr;

            $condition_res = $this->check_list_exist($input_params);


            if ($condition_res["checkListExist"]["status"])
            {
               $input_params = $this->delete_list($input_params);
             
               $output_response = $this->delete_list_finish_success($input_params);
                return $output_response;
            }

            else
            {
                $output_response = $this->delete_list_finish_success_1($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            //log_message('error', $e->getMessage());
        }
        return $output_response;  
    }

    /**
     * get_unread_count method is used to process custom function.
     * @created kavita sawant |  04.03.2021
     * @modified kavita sawant | 04.03.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_unread_count($input_params = array())
    {
        if (!method_exists($this, "getUnreadNotificationCount"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->getUnreadNotificationCount($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["get_unread_count"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * delete_list method is used to process review block.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_list($input_params = array())
    {

      $this->block_result = array();
        try
        {
            $arrResult = array();

            $arrResult['list_id']  = isset($input_params["list_id"]) ? $input_params["list_id"] : "";
            $this->block_result = $this->my_list_model->delete_list($arrResult);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

          $this->block_result["data"] = $result_arr;
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_list"] = $this->block_result["data"];

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }


     /**
     * delete_list_finish_success method is used to process finish flow.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process loop flow.
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

        $func_array["function"]["name"] = "delete_list";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    /**
     * delete_list_finish_success method is used to process finish flow.
     * @created Suresh Nakate
     * @param array $input_params input_params array to process loop flow.
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

        $func_array["function"]["name"] = "delete_list";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * get_list_finish_success method is used to process finish flow.
     * @created Suresh Nakate | 08.03.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    


    public function get_list_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "get_list_finish_success"
        );
         $output_fields = array(
                "list_id",
                "list_name",
                "address",
                "from_country_code",
                "from_country_name",
                "from_currency",
                "continent",
                "To_country_code",
                "To_country_name",
                "To_country_name",
                "created_date",
             );
            $output_keys = array(
                'get_all_list',
            );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_my_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);


        return $responce_arr;
    }

    /**
     * get_list_finish_success method is used to process finish flow.
     * @created Suresh Nakate | 08.03.2021
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_list_finish_success_1($input_params = array(),$result_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_list_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_my_list";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    

}
