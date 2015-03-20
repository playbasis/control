<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Input extends CI_Input{
	public function __construct(){
		parent::__construct();
	}

	public function checkParam($keys){
		if(!$keys)
			return false;

		$required = array();
		foreach ($keys as $key) {
			if(!parent::get_post($key)){
				array_push($required,$key);
			}
		}

		return $required;
	}

    public function checkParamPut($keys, $args) {
        if (!$keys) return false;
        $required = array();
        $args_keys = array_keys($args);
        foreach ($keys as $key) {
            if (!in_array($key, $args_keys)) {
                array_push($required, $key);
            }
        }
        return $required;
    }

    function ip_address()
    {
        if ($this->ip_address !== FALSE)
        {
            return $this->ip_address;
        }

        foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
            if (($spoof = $this->server($header)) !== FALSE)
            {
                // Some proxies typically list the whole chain of IP
                // addresses through which the client has reached us.
                // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                if (strpos($spoof, ',') !== FALSE)
                {
                    $spoof = explode(',', $spoof, 2);
                    $spoof = $spoof[0];
                }

                if ($this->valid_ip($spoof))
                {
                    $this->ip_address = $spoof;
                    break;
                }
            }
        }

        if ($this->ip_address === FALSE)
        {
            $this->ip_address = $this->server('remote_addr');
        }

        return $this->ip_address;
    }
}
?>
