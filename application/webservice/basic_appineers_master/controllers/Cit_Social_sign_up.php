<?php


/**
 * Description of Social Sign Up Extended Controller
 * 
 * @module Extended Social Sign Up
 * 
 * @class Cit_Social_sign_up.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Social_sign_up.php
 * 
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Social_sign_up extends Social_sign_up
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Used to check unique user.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $return_arr return unique user status & message.
     */
    public function checkUniqueUser($input_params = array())
    {
        
        $return_arr['message'] = '';
        $return_arr['status'] = '1';
        try {

        //check social signup unique user
        try {
            $this->db->select('eSocialLoginType,vSocialLoginId');
            $this->db->from('users');
            $this->db->where('eSocialLoginType', $input_params['social_login_type']);
            $this->db->where('vSocialLoginId', $input_params['social_login_id']);
            $social_data = $this->db->get()->result_array();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!empty($social_data)) {
                $return_arr['message'] = "This social account already registered, please try using different account.";
                $return_arr['status'] = "0";

                return  $return_arr;
            }
            //check unique email
            if (!empty($input_params['email'])) {


                $this->db->select('vEmail');
                $this->db->from('users');
                $this->db->where('vEmail', $input_params['email']);
                $email_data = $this->db->get()->result_array();
                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }
                if ($email_data[0]['vEmail'] == $input_params['email']) {
                    $return_arr['message'] = "This email is already registered, please try using different email.";
                    $return_arr['status'] = "0";
                    return  $return_arr;
                }
            }
            //check unique mobile number
            if (!empty($input_params['mobile_number'])) {
                $this->db->select('vMobileNo');
                $this->db->from('users');
                $this->db->where('vMobileNo', $input_params['mobile_number']);
                $mobile_number_data = $this->db->get()->result_array();
                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }
                if ($mobile_number_data[0]['vMobileNo'] == $input_params['mobile_number']) {
                    $return_arr['message'] = "This mobile number already registered, please try using different mobile number.";
                    $return_arr['status'] = "0";
                    return  $return_arr;
                }
            }

            //check unique username
            if (!empty($input_params['user_name'])) {
                $this->db->select('vUserName');
                $this->db->from('users');
                $this->db->where('vUserName', $input_params['user_name']);
                $user_name_data = $this->db->get()->result_array();
                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }
                if ($user_name_data[0]['vUserName'] == $input_params['user_name']) {
                    $return_arr['message'] = "This username already registered, please try using different username.";
                    $return_arr['status'] = "0";
                    return  $return_arr;
                }
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
    } catch (Exception $e) {
        $input_params['db_query'] = $this->db->last_query();
        $this->general->apiLogger($input_params, $e);
        $success = 0;
        $message = $e->getMessage();
    }

        return  $return_arr;
    }

    /**
     * Get terms and conditions version.
     * 
     * @return array $termsconditions_code_version return terms conditions code version.
     */
    public function getTermsConditionVersion()
    {
        try {
            $this->db->select('vVersion');
            $this->db->from('mod_page_settings');
            $this->db->where_in('vPageCode', 'termsconditions');
            $termsconditions_code_version = $this->db->get();

            $termsconditions_code_version = is_object($termsconditions_code_version) ? $termsconditions_code_version->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
        }

        return $termsconditions_code_version[0]['vVersion'];
    }

    /**
     * Get privacy policy version.
     * 
     * @return array $privacypolicy_code_version return privacy policy version.
     */
    public function getPrivacyPolicyVersion()
    {
        try {
            $this->db->select('vVersion');
            $this->db->from('mod_page_settings');
            $this->db->where_in('vPageCode', 'privacypolicy');
            $privacypolicy_code_version = $this->db->get();

            $privacypolicy_code_version = is_object($privacypolicy_code_version) ? $privacypolicy_code_version->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
        }

        return $privacypolicy_code_version[0]['vVersion'];
    }
}
