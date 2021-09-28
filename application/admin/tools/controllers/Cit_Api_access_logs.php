<?php


/**
 * Description of API access logs Extended Controller
 *
 * @module Extended API access logs
 *
 * @class Cit_Api_access_logs.php
 *
 * @path application\admin\tools\controllers\Cit_Api_access_logs.php
 *
 * @author CIT Dev Team
 *
 * @date 29.09.2020
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Api_access_logs extends Api_access_logs
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cit_Api_access_logs_model');
        $this->load->model('cit_api_model');
        $this->deleteOldApiLogs();
    }
    public function deleteOldApiLogs()
    {
        $this->cit_api_model->callAPI('delete_api_log');
    }
    public function showInputParamsLink($value = '', $id = '', $data = array())
    {
        if (!empty($value)) {
            $ret_str = '<a title="Click Here" href="' . $this->config->item('admin_url') . '#' . $this->general->getAdminEncodeURL("tools/api_access_logs/viewInputParams") . '|id|' . $this->general->getAdminEncodeURL($id) . '" class="fancybox-popup"> Click Here </a>';
            return $ret_str;
        } else {
            return $value;
        }
    }
    public function showInputResponseLink($value = '', $id = '', $data = array())
    {
        if (!empty($value)) {
            $ret_str = '<a title="Click Here" href="' . $this->config->item('admin_url') . '#' . $this->general->getAdminEncodeURL("tools/api_access_logs/viewInputResponse") . '|id|' . $this->general->getAdminEncodeURL($id) . '" class="fancybox-popup"> Click Here </a>';
            return $ret_str;
        } else {
            return $value;
        }
    }
    public function listingValue($value = '', $id = '', $data = array())
    {
        if (empty($data['aa_performed_by']) && $data['aa_performed_by'] != '0') {
            $value = "N/A";
        } elseif ($data['aa_performed_by'] == '0') {
            $value = "N/A";
        }
        return $value;
    }

    public function viewInputParams()
    {
        $id = $this->input->get_post("id", true);
        // $fields = "vFileName";
        // $db_data = $this->Cit_Api_access_logs_model->get_input_params($id, $fields);
        // $db_data = $this->api_access_logs_model->get_input_params($id, $fields);
            // $access_log_folder = $this->config->item('admin_access_log_path');
            // $log_folder_path = $access_log_folder . "api_logs" . DS;
            // $log_file_path = $log_folder_path . $db_data['vFileName'];

            // $json_data = file_get_contents($log_file_path);
            // $rec_data = json_decode($json_data, true);

           
            $fields = "jInputParam AS input_params";
            $db_data = $this->Cit_Api_access_logs_model->get_input_params($id, $fields);
            $rec_data = array("input_params" => json_decode($db_data['input_params'], true));

            $data_arr['data'] = $rec_data;
            $this->smarty->assign($data_arr);

            $this->loadView("api_access_logs_cit_input_params");
    }

    public function viewInputResponse()
    {
        $id = $this->input->get_post("id", true);
        $show_queries = $this->config->item('SHOW_QUERIES');
        $fields = "vFileName";
        $db_data = $this->Cit_Api_access_logs_model->get_input_params($id, $fields);
        // $db_data = $this->api_access_logs_model->get_input_params($id, $fields);
        if (!empty($db_data)) {
            // $access_log_folder = $this->config->item('admin_access_log_path');
            // $log_folder_path = $access_log_folder . "api_logs" . DS;
            // $log_file_path = $log_folder_path . $db_data['vFileName'];

            // $json_data = file_get_contents($log_file_path);
            // $rec_data = json_decode($json_data, true);

            // if (strtolower($show_queries) == 'no') {
            //     unset($rec_data['output_response']['queries']);
            // }

            if ($db_data['vFileName'] == '0') {
                $fields = "jOutputResponse AS output_response";
                $db_data = $this->Cit_Api_access_logs_model->get_input_params($id, $fields);
                $json_data = $db_data['output_response'];
                $rec_data = json_decode($json_data, true);

                if (strtolower($show_queries) == 'no') {
                    unset($rec_data['output_response']['queries']);
                }
            } else {
                $fields = "vErrorFile AS Error_file, iErrorCode AS error_code,vErrorMessage AS error_message,
                vDbQuery AS db_query, lErrorStack AS error_stack";
                $db_data = $this->Cit_Api_access_logs_model->get_input_params($id, $fields);
                $rec_data = array("output_response" => $db_data);
            }

            $data_arr['data'] = $rec_data;
            $this->smarty->assign($data_arr);
            $this->loadView("api_access_logs_cit_output_response");
        }
    }
}
