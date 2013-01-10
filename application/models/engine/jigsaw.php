<?php defined('BASEPATH') OR exit('No direct script access allowed');
class jigsaw extends CI_Model{

	public function __construct(){
		parent::__construct();

		//load model

	}
	
	//action jigsaw
	public function action($config,$input){
		
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['url']));
		
		//validate url
		if($config['url']){
			return (boolean) $this->matchUrl($input['url'],$config['url'],$config['regex']);
		}
		else{
			return true;
		}
	}

	//reward jigsaw
	public function reward($config,$input){
		assert($config != false);
		assert(is_array($config));		
		assert(isset($config['reward_id']));
		assert(isset($config['reward_name']));
		assert(isset($config['item_id']));
		assert(isset($config['quantity']));

		assert($input != false);
		assert(is_array($input));
		assert($input['pb_player_id']);

		//always true if reward type is point
		if(is_null($config['item_id']))
			return true;


		//if reward type is badge
		switch ($config['reward_name']) {
			case 'badge':
				//return function to process badge
				break;
			default:
				return false;
				break;
		}

	}

	//util :: badge checker
	public function checkbadge($badgeId,$pb_player_id){
		//get badge properties
		$this->db->select('stackable,substract,quantity');
		$this->db->where(array('badge_id'=>$badgeId));
		$result = $this->db->get('playbasis_badge');

		$badgeInfo = $result->row_array();
		#not finish yet

		//search badge own by player
		$this->db->where(array('badge_id'=>$badgeId,'pb_player_id'=>$pb_player_id));
		$this->db->from('playbasis_badge_to_player');
		$haveBadge = $this->db->count_all_results();

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
			$match;
			if($isRegEx){
				$match = preg_match($compareUrl, $inputUrl);
			}
			else{
				$match = (string)$compareUrl === (string)$inputUrl;
			}
			
			return $match;
			//e.g.
			//inputurl domain/forum/hello-my-new-notebook
			//input domain/forum/test1234
			//url = domain/forum/(a-zA-Z0-9\_\-)+
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