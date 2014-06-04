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
}
?>
