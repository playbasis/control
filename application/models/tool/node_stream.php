<?php defined('BASEPATH') OR exit('No direct script access allowed');

define('STREAM_URL', 'http://dev.pbapp.net/activitystream/');
define('STREAM_PORT', 3000);
define('USERPASS','planescape:torment');

class Node_stream extends CI_Model{
	public function publish($data,$info){
		//prepare chanel name
		$chanelName = preg_replace( '/(http[s]?:\/\/)?([w]{3}\.)?/' , '' , $info['domain_name']);

		//set curl
		$ch = curl_init();

		var_dump(json_encode($data));
		//set curl option
		curl_setopt($ch, CURLOPT_URL, STREAM_URL.$chanelName);											# set url
		curl_setopt($ch, CURLOPT_PORT, STREAM_PORT);													# set port
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);    											# refuse response from called server
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json; charset=utf-8'));	# set Content-Type
    	curl_setopt($ch, CURLOPT_TIMEOUT, 10); 															# time for execute
 		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);														# time for try to connect 
    	curl_setopt($ch, CURLOPT_POST, TRUE);															# use POST 
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));										# wrap data 
    	// curl_setopt($ch, CURLOPT_USERPWD, USERPASS);													# http authrn user password
    	// curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);											# http authen
    	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    	// curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    	//process 
    	curl_exec($ch);


    	//debugging
  		var_dump(curl_errno($ch));
 				
	 	$cinfo = curl_getinfo($ch);
	 	var_dump($cinfo);

    	//close connection
    	curl_close($ch);
	}	
}
?>