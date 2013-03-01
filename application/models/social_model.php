<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/facebook-php-sdk/facebook.php';

class Social_model extends CI_Model{
	
	private $facebook = null;
	
	public function __construct(){
		parent::__construct();

		$this->load->model('player_model');
		$this->load->model('client_model');
		
		$config = array();
		$config['appId'] = '421530621269210';
		$config['secret'] = '6544951f29daa3afe9c7ad4da7b3d88b';
		$config['fileUpload'] = false; // optional

		$this->facebook = new Facebook($config);
	}
	
	public function processFacebookData($changedData){
		
		$pb_player_id = 0;
		$client_id = 0;
		$site_id = 0;
		$action = '';
		$message = '';
		
		if($changedData['object'] == "page"){
			
			$entries = $changedData['entry'];
			foreach($entries as $entry){
				
				$changes = $entry['changes'];
				$id = $entry['id'];
				$client = $this->getClientFromFacebookPageId($id);
				$client_id = $client['client_id'];
				$site_id = $client['site_id'];
				
				foreach($changes as $changed){
					
					if($changed['field'] != 'feed')
						continue;
					
					$value = $changed['value'];
					$item = $value['item'];
					$verb = $value['verb'];
					
					if($item == 'status' && $verb == 'add'){
						$postId = $this->formatFacebookPostId($value['post_id'], $id);
						$data = $this->getFacebookPostData($postId);
						$senderId = $data['from_id'];
						$message = $data['message'];
						$pb_player_id = $this->getPBPlayerIdFromFacebookId($senderId);
						$action = 'fb' . $item;
					}
					else if($item == 'post' && $verb == 'add'){
						$postId = $this->formatFacebookPostId($value['post_id'], $id);
						$data = $this->getFacebookPostData($postId);
						$senderId = $data['from_id'];
						$message = $data['message'];
						$pb_player_id = $this->getPBPlayerIdFromFacebookId($senderId);
						$action = 'fb' . $item;
					}
					else if($item == 'comment' && $verb == 'add'){
						$senderId = $value['sender_id'];
						$commentId = $this->formatFacebookCommentId($value['comment_id'], $value['parent_id'], $id);
						$data = $this->getFacebookCommentData($commentId);
						$message = $data['message'];
						$pb_player_id = $this->getPBPlayerIdFromFacebookId($senderId);
						$action = 'fb' . $item;
					}
					else if($item == 'like' && $verb == 'add'){
						$senderId = $value['sender_id'];
						$parentId = $value['parent_id'];
						$pb_player_id = $this->getPBPlayerIdFromFacebookId($senderId);
						$action = 'fb' . $item;
					}
				}
			}
		}
		
		if(!$pb_player_id || !$action)
			return false;
		
		return array('pb_player_id' => $pb_player_id, 
					 'client_id' => $client_id,
					 'site_id' => $site_id,
					 'action' => $action,
					 'message' => $message);
	}
	
	public function getClientFromFacebookPageId($facebook_page_id){
		
		if(!is_string($facebook_page_id))
			$facebook_page_id = $this->bigIntToString($facebook_page_id);
		
		$this->db->select('client_id,site_id');
		$this->db->where('facebook_page_id', $facebook_page_id);
		$result = $this->db->get('playbasis_facebook_page_to_client');
		return $result->row_array();
	}
	
	public function getClientFromHashTag($hashtag){
		assert(is_string($hashtag));
		$this->db->select('client_id,site_id');
		$this->db->where('hashtag', $hashtag);
		$result = $this->db->get('playbasis_hashtag_to_client');
		return $result->row_array();
	}
	
	public function getPBPlayerIdFromFacebookId($facebook_id){
		
		if(!is_string($facebook_id))
			$facebook_id = $this->bigIntToString($facebook_id);
		
		$this->db->select('pb_player_id');
		$this->db->where('facebook_id', $facebook_id);
		$result = $this->db->get('playbasis_player');
		$result = $result->row_array();
		return ($result) ? $result['pb_player_id'] : false;
	}
	
	public function getPBPlayerIdFromTwitterId($twitter_id){
		assert(is_string($twitter_id));
		$this->db->select('pb_player_id');
		$this->db->where('twitter_id', $twitter_id);
		$result = $this->db->get('playbasis_player');
		$result = $result->row_array();
		return ($result) ? $result['pb_player_id'] : false;
	}
	
	private function formatFacebookPostId($postId, $pageId){
		
		if(!is_string($postId))
			$postId = $this->bigIntToString($postId);
		if(!is_string($pageId))
			$pageId = $this->bigIntToString($pageId);
		
		$postIds = explode("_", $postId);
		if(count($postIds) == 1){
			$postId = $pageId . '_' . $postId;
		}
		return $postId;
	}
	
	private function formatFacebookCommentId($commentId, $postId, $pageId){
		
		if(!is_string($commentId))
			$commentId = $this->bigIntToString($commentId);
		if(!is_string($postId))
			$postId = $this->bigIntToString($postId);
		if(!is_string($pageId))
			$pageId = $this->bigIntToString($pageId);
		
		$commentIds = explode("_", $commentId);
		if(count($commentIds) == 1){
			$parentId = formatPostId($postId, $pageId);
			$commentId = $parentId . '_' . $commentId;
		}
		return $commentId;
	}
	
	private function getFacebookPostData($postId){
		assert(is_string($postId));
		$result = array();
		try {
			$postData = $this->facebook->api('/' . $postId);
		} catch(FacebookApiException $e) {
			return $result;
		}
		$result['from_name'] = $postData['from']['name'];
		$result['from_id'] = $postData['from']['id'];
		$result['message'] = $postData['message'];
		return $result;
	}
	
	private function getFacebookCommentData($commentId){
		return $this->getFacebookPostData($commentId);
	}
	
	private function bigIntToString($number){
		$numStr = serialize($number);
		return substr($numStr, 2,-1);
	}
}


/*
sample data from facebook

//add status
{
    "object": "page",
    "entry": [
        {
            "id": "500158316696377",
            "time": 1361535914,
            "changes": [
                {
                    "field": "feed",
                    "value": {
                        "item": "status",
                        "verb": "add",
                        "post_id": "500158316696377_500219983356877"
                    }
                }
            ]
        }
    ]
}

//add post
{
    "object": "page",
    "entry": [
        {
            "id": "500158316696377",
            "time": 1361951210,
            "changes": [
                {
                    "field": "feed",
                    "value": {
                        "item": "post",
                        "verb": "add",
                        "post_id": 502116146500594
                    }
                }
            ]
        }
    ]
}

//add comment
{
    "object": "page",
    "entry": [
        {
            "id": "500158316696377",
            "time": 1361535969,
            "changes": [
                {
                    "field": "feed",
                    "value": {
                        "item": "comment",
                        "verb": "add",
                        "comment_id": "500158316696377_500219983356877_5405676",
                        "parent_id": "500158316696377_500219983356877",
                        "sender_id": 500158316696377,
                        "created_time": 1361535969
                    }
                }
            ]
        }
    ]
}

//like on a post or status
{
    "object": "page",
    "entry": [
        {
            "id": "500158316696377",
            "time": 1361952400,
            "changes": [
                {
                    "field": "feed",
                    "value": {
                        "item": "like",
                        "verb": "add",
                        "parent_id": "500158316696377_500219983356877",
                        "sender_id": 802465011,
                        "created_time": 1361952400
                    }
                }
            ]
        }
    ]
}

//change page picture
{
    "object": "page",
    "entry": [
        {
            "id": "500158316696377",
            "time": 1361535940,
            "changes": [
                {
                    "field": "picture"
                }
            ]
        }
    ]
}

//change page name
{
    "object": "page",
    "entry": [
        {
            "id": "500158316696377",
            "time": 1361952110,
            "changes": [
                {
                    "field": "name"
                }
            ]
        }
    ]
}

//data of a status
{
  "id": "500158316696377_500219983356877", 
  "from": {
    "category": "Computers/technology", 
    "name": "Privatesandbox", 
    "id": "500158316696377"
  }, 
  "message": "test test test", 
  "actions": [
    {
      "name": "Comment", 
      "link": "http://www.facebook.com/500158316696377/posts/500219983356877"
    }, 
    {
      "name": "Like", 
      "link": "http://www.facebook.com/500158316696377/posts/500219983356877"
    }
  ], 
  "privacy": {
    "description": "Public", 
    "value": "EVERYONE", 
    "friends": "", 
    "networks": "", 
    "allow": "", 
    "deny": ""
  }, 
  "type": "status", 
  "status_type": "mobile_status_update", 
  "created_time": "2013-02-22T12:25:14+0000", 
  "updated_time": "2013-02-27T08:55:31+0000", 
  "likes": {
    "data": [
      {
        "name": "Maethee Chongchitnant", 
        "id": "802465011"
      }
    ], 
    "count": 1
  }, 
  "comments": {
    "data": [
      {
        "id": "500158316696377_500219983356877_5405676", 
        "from": {
          "category": "Computers/technology", 
          "name": "Privatesandbox", 
          "id": "500158316696377"
        }, 
        "message": "test comments", 
        "created_time": "2013-02-22T12:26:09+0000"
      }, 
      {
        "id": "500158316696377_500219983356877_5421631", 
        "from": {
          "name": "Maethee Chongchitnant", 
          "id": "802465011"
        }, 
        "message": "comment test", 
        "created_time": "2013-02-27T08:55:31+0000"
      }
    ], 
    "count": 2
  }
}

//data of a post
{
  "id": "500158316696377_502116146500594", 
  "from": {
    "name": "Maethee Chongchitnant", 
    "id": "802465011"
  }, 
  "to": {
    "data": [
      {
        "category": "Computers/technology", 
        "name": "Privatesandbox", 
        "id": "500158316696377"
      }
    ]
  }, 
  "message": "test post as me", 
  "actions": [
    {
      "name": "Comment", 
      "link": "http://www.facebook.com/500158316696377/posts/502116146500594"
    }, 
    {
      "name": "Like", 
      "link": "http://www.facebook.com/500158316696377/posts/502116146500594"
    }
  ], 
  "privacy": {
    "value": ""
  }, 
  "type": "status", 
  "created_time": "2013-02-27T07:46:50+0000", 
  "updated_time": "2013-02-27T07:46:50+0000", 
  "comments": {
    "count": 0
  }
}

//data on comment
{
  "id": "500158316696377_500219983356877_5405676", 
  "from": {
    "category": "Computers/technology", 
    "name": "Privatesandbox", 
    "id": "500158316696377"
  }, 
  "message": "test comments", 
  "can_remove": true, 
  "created_time": "2013-02-22T12:26:09+0000", 
  "like_count": 0, 
  "user_likes": false
}

*/
