<?php


/**
 * Description of Social Login Extended Controller
 * 
 * @module Extended Social Login
 * 
 * @class Cit_Social_login.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Social_login.php
 * 
 */

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Cit_Social_login extends Social_login
{
	/**
	 * To initialize class objects/variables.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Used to prepare where condition.
	 *
	 * @param array $input_params input_params array to process loop flow.
	 *
	 * @return array $return_arr return where condition.
	 */
	public function helperPrepareWhere(&$input_params = array())
	{
		$allowed_sn = array('facebook', 'google', 'apple');
		$return = array();
		$return[0]['status'] = 1;
		$return[0]['message'] = "";
		$return[0]['where_clause'] = '0=1';
		$where = array();
		if ($input_params['social_login_type'] != '' && $input_params['social_login_id'] != '') {
			if (in_array($input_params['social_login_type'], $allowed_sn)) {
				$where[] = "u.vSocialLoginId='" . trim($input_params['social_login_id']) . "' ";
				$where[] = "u.eSocialLoginType IN('" . trim($input_params['social_login_type']) . "') ";
			} else {
				$return[0]['status'] = 0;
				$return[0]['message'] = "we are supporting facebook,google and apple account for login.";
			}
		} else {
			$return[0]['status'] = 0;
			$return[0]['message'] = "Please provide login detail.";
		}

		$return[0]['where_clause'] = implode("AND ", $where);

		return $return;
	}
}
