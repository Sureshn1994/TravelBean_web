<?php
/**
 * CodeIgniter Log Library
 *
 * @category   Applications
 * @package    CodeIgniter
 * @subpackage Libraries
 * @author     Bo-Yi Wu <appleboy.tw@gmail.com>
 * @license    BSD License
 * @link       http://blog.wu-boy.com/
 * @since      Version 1.0
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Lib_log
{
    /**
     * ci
     *
     * @param instance object
     */
    private $CI;

    /**
     * log table name
     *
     * @param string
     */
    private $_log_table_name;

    public $levels = array(
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parsing Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable error',
        E_DEPRECATED        => 'Runtime Notice',
        E_USER_DEPRECATED   => 'User Warning'
    );

    /**
     * constructor
     *
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        set_error_handler(array($this, 'error_handler'));
        set_exception_handler(array($this, 'exception_handler'));
      
        // Load config file
        //$this->CI->load->config('log');
        //$this->_log_table_name = ($this->CI->config->item('log_table_name')) ? $this->CI->config->item('log_table_name') : 'logs';
    }

    /**
     * PHP Error Handler
     *
     * @param   int
     * @param   string
     * @param   string
     * @param   int
     * @return void
     */
    public function error_handler($severity, $message, $filepath, $line)
    {
        $this->CI =& get_instance();

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $plat_form = $this->get_platform($user_agent);
        
        $browser = $this->get_browser($user_agent);

        $level = strtoupper($this->levels[$severity]);
        $this->CI->benchmark->mark('code_end');

        if ($level == 'notice') {
            $data = array(
            'iPerformedBy' => $this->getUserIdFromAccessToken(),
            'vPlatform' => $plat_form,
            'vBrowser' => $browser,
            'vIPAddress' => $this->CI->input->ip_address(),
            'vAPIName' => $this->CI->uri->segments[2],
            'vAPIURL' => $this->getRequestUrl(),
            'vRequestMethod' => $_SERVER['REQUEST_METHOD'],
            'dtExecutedDate' => date('Y-m-d H:i:s'),
            'dAccessDate' => date('Y-m-d H:i:s'),
            'vErrorType' => isset($this->levels[$severity]) ? $this->levels[$severity] : $severity,
            'vErrorMessage' => $message,
            'iErrorCode' => isset($this->levels[$severity]) ? $this->levels[$severity] : $severity,
            'vErrorFile' => $filepath . ' Line:' .$line,
            'fExcutionTime' => $this->CI->benchmark->elapsed_time('code_start', 'code_end')
        );
            $this->CI->db->flush_cache();
            $this->CI->db->_reset_all();
            $result = $this->CI->db->insert('api_accesslogs', $data);
            $this->CI->db->trans_commit();
        }
        // $this->CI->db->insert($this->_log_table_name, $data);
    }

    /**
     * PHP Error Handler
     *
     * @param object
     * @return void
     */
    public function exception_handler($exception)
    {
        $this->CI =& get_instance();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $plat_form = $this->get_platform($user_agent);
        $browser = $this->get_browser($user_agent);
        
        $this->CI->benchmark->mark('code_end');
        $data = array(
            'iErrorCode' => $exception->getCode(),
            'iPerformedBy' => $this->getUserIdFromAccessToken(),
            'vPlatform' => $plat_form,
            'vBrowser' => $browser,
            'vIPAddress' => $this->CI->input->ip_address(),
            'vAPIName' => $this->CI->uri->segments[2],
            'vAPIURL' => $this->getRequestUrl(),
            'vRequestMethod' => $_SERVER['REQUEST_METHOD'],
            'dtExecutedDate' => date('Y-m-d H:i:s'),
            'dAccessDate' => date('Y-m-d H:i:s'),
            'vErrorType' => 'Error',
            'vErrorMessage' => $exception->getMessage(),
            'lErrorStack' => $exception,
            'iErrorCode' => $exception->getCode(),
            'vErrorFile' => $exception->getFile() . ' Line:' . $exception->getLine(),
            'fExcutionTime' => $this->CI->benchmark->elapsed_time('code_start', 'code_end')
        );

        $this->CI->db->flush_cache();
        $this->CI->db->_reset_all();
        $result = $this->CI->db->insert('api_accesslogs', $data);
        $this->CI->db->trans_commit();
    }

    public function getUserIdFromAccessToken()
    {
        $auth_header ="";
        $userid = 0;
        //JWT token verification
        $auth_header = $this->CI->input->get_request_header('AUTHTOKEN');

        if ($auth_header != "") {
            $req_token = $auth_header;
            $this->CI->db->select('iUserId');
            $this->CI->db->from('users');
            $this->CI->db->where('vAccessToken', $req_token);
            $result = $this->CI->db->get()->result_array();
            $userid = $result[0]['iUserId'];
        }

        return $userid;
    }

    public function getRequestUrl()
    {
        // Access Logs insertion => start
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $request_url = "https://";
        } else {
            $request_url = "http://";
        }
        // Append the host(domain name, ip) to the URL.
        $request_url.= $_SERVER['HTTP_HOST'];
        
        // Append the requested resource location to the URL
        $request_url.= $_SERVER['REQUEST_URI'];

        return $request_url;
    }

    public function get_platform($user_agent = '')
    {
        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }

    public function get_browser($user_agent = '')
    {
        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser',
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }
}

/* End of file Lib_log.php */
