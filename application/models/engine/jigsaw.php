<?php defined('BASEPATH') OR exit('No direct script access allowed');
class jigsaw extends CI_Model{

	public function __construct(){
		parent::__construct();

		//load model

	}
	
	//action jigsaw
	public function action($input,$option){
		
		assert($input != false);
		assert(unserialize($input));
		assert(isset($option['url']));

		//unserialize input
		$inputSet = unserialize($input);
		
		//validate url
		if($inputSet['url']){

		}
	}

	//util :: url matching
	public function matchUrl($inputUrl,$compareUrl,$isRegEx){
			

			$urlFragment = parse_url($inputUrl);

			//check posible index page
			if(!$urlFragment['path'])
				$inputUrl = '/';
			if($urlFragment['path'] == '/')
				$inputUrl = '/'
			if(preg_match('/\/index\.[a-zA-Z]{3,}$/', $urlFragment['path']))   // match all  "/index.*" 
				$inputUrl = '/'
			if(preg_match('/\/index\.[a-zA-Z]{3,}\/$/', $urlFragment['path']))   // match all  "/index.*/" 
				$inputUrl = '/'

			//check query
			if($urlFragment['query'])
				$inputUrl.= '?'.$urlFragment['query'];

			//check fragment
			if($urlFragment['fragment'])
				$inputUrl.= '#'.$urlFragment['fragment'];



			//compare url

			input domain/forum/hello-my-new-notebook
			input domain/forum/test1234
			

			url = domain/forum/(a-zA-Z0-9\_\-)+
	}



































	// public function findAction($action,$client){
	// 	assert($action != false);
	// 	assert('is_array($client)');
	// 	assert('!empty($client)');
		
	// 	$this->db->select('action_id');
	// 	$this->db->where(array('client_id'=>$client['client_id'],'site_id'=>$client['site_id'],'name'=>$action));

	// 	$result = $this->db->get('playbasis_action_to_client');

	// 	return $result->row_array(); 
	// }
}
?>