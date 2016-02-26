<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/facebook-php-sdk/facebook.php';

class Social_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('player_model');
        $this->load->model('client_model');
        $this->load->library('mongo_db');
    }

    private function getFacebookCredentials($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'app_id',
            'app_secret'
        ));
        $this->mongo_db->where(array(
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $result = $this->mongo_db->get('playbasis_facebook_page_to_client');
        return ($result) ? $result[0] : $result;
    }

    private function getFacebookObject($client_id, $site_id)
    {
        $credentials = $this->getFacebookCredentials($client_id, $site_id);
        $config = array();
        $config['appId'] = $credentials['app_id'];
        $config['secret'] = $credentials['app_secret'];
        $config['fileUpload'] = false; // optional
        return new Facebook($config);
    }

    public function processTwitterData($tweetData)
    {
        $this->set_site_mongodb(0);
        $mongoDate = new MongoDate(time());
        $this->mongo_db->insert('twitter_log', array_merge($tweetData, array(
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        )));
        $twitter_id = $tweetData['user']['id_str'];
        $action = 'tweet';
        $message = $tweetData['text'];
        $pb_player_id = 0;
        $client_id = 0;
        $site_id = 0;
        $sites = array();
        $dupeSites = array();
        preg_match_all("/(#\w+)/", $message, $matches);
        foreach ($matches[0] as $hashtag) {
            $client = $this->getClientFromHashTag($hashtag);
            if (!$client) {
                continue;
            }
            $client_id = $client['client_id'];
            $site_id = $client['site_id'];
            $pb_player_id = $this->getPBPlayerIdFromTwitterId($twitter_id, $client_id, $site_id);
            if (!$pb_player_id || isset($dupeSites[$site_id])) {
                continue;
            }
            $dupeSites[$site_id] = true;
            array_push($sites, array(
                'pb_player_id' => $pb_player_id,
                'client_id' => $client_id,
                'site_id' => $site_id
            ));
        }
        return array(
            'twitter_id' => $twitter_id,
            'action' => $action,
            'message' => $message,
            'sites' => $sites
        );
    }

    public function processFacebookData($changedData)
    {
        $this->set_site_mongodb(0);
        $mongoDate = new MongoDate(time());
        $this->mongo_db->insert('facebook_log', array_merge($changedData, array(
            'date_added' => $mongoDate,
            'date_modified' => $mongoDate
        )));
        $pb_player_id = 0;
        $facebook_id = 0;
        $client_id = 0;
        $site_id = 0;
        $action = '';
        $message = '';
        if ($changedData['object'] == "page") {
            $entries = $changedData['entry'];
            foreach ($entries as $entry) {
                $changes = $entry['changes'];
                $id = $entry['id'];
                $client = $this->getClientFromFacebookPageId($id);
                if (!$client) {
                    continue;
                }
                $client_id = $client['client_id'];
                $site_id = $client['site_id'];
                foreach ($changes as $changed) {
                    if ($changed['field'] != 'feed') {
                        continue;
                    }
                    $value = $changed['value'];
                    $item = $value['item'];
                    $verb = $value['verb'];
                    if ($item == 'status' && $verb == 'add') {
                        $postId = $this->formatFacebookPostId($value['post_id'], $id);
                        $data = $this->getFacebookPostData($client_id, $site_id, $postId);
                        if ($data) {
                            $facebook_id = $data['from_id'];
                            $message = $data['message'];
                            $pb_player_id = $this->getPBPlayerIdFromFacebookId($facebook_id, $client_id, $site_id);
                        }
                        $action = 'fb' . $item;
                    } else {
                        if ($item == 'post' && $verb == 'add') {
                            $postId = $this->formatFacebookPostId($value['post_id'], $id);
                            $data = $this->getFacebookPostData($client_id, $site_id, $postId);
                            if ($data) {
                                $facebook_id = $data['from_id'];
                                $message = $data['message'];
                                $pb_player_id = $this->getPBPlayerIdFromFacebookId($facebook_id, $client_id, $site_id);
                            }
                            $action = 'fb' . $item;
                        } else {
                            if ($item == 'comment' && $verb == 'add') {
                                $facebook_id = $value['sender_id'];
                                $commentId = $this->formatFacebookCommentId($value['comment_id'], $value['parent_id'],
                                    $id);
                                $data = $this->getFacebookCommentData($client_id, $site_id, $commentId);
                                if ($data) {
                                    $message = $data['message'];
                                }
                                $pb_player_id = $this->getPBPlayerIdFromFacebookId($facebook_id, $client_id, $site_id);
                                $action = 'fb' . $item;
                            } else {
                                if ($item == 'like' && $verb == 'add') {
                                    $facebook_id = $value['sender_id'];
                                    $parentId = $value['parent_id'];
                                    $pb_player_id = $this->getPBPlayerIdFromFacebookId($facebook_id, $client_id,
                                        $site_id);
                                    $action = 'fb' . $item;
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            'pb_player_id' => $pb_player_id,
            'facebook_id' => $facebook_id,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'action' => $action,
            'message' => $message
        );
    }

    private function getFacebookAppAccessToken($client_id, $site_id)
    {
        $credentials = $this->getFacebookCredentials($client_id, $site_id);
        $app_token_url = "https://graph.facebook.com/oauth/access_token?" . "client_id=" . $credentials['app_id'] . "&client_secret=" . $credentials['app_secret'] . "&grant_type=client_credentials";
        $response = file_get_contents($app_token_url);
        $params = null;
        parse_str($response, $params);
        return $params['access_token'];
    }

    public function sendFacebookNotification($client_id, $site_id, $facebook_id, $message, $href)
    {
        $result = null;
        try {
            $facebook = $this->getFacebookObject($client_id, $site_id);
            $appAccessToken = $this->getFacebookAppAccessToken($client_id, $site_id);
            $facebook->setAccessToken($appAccessToken);
            $result = $facebook->api('/' . $facebook_id . '/notifications', 'POST', array(
                'href' => $href,
                'template' => '@[' . $facebook_id . '] ' . $message
            ));
        } catch (FacebookApiException $e) {
            error_log(json_encode($e->getResult()));
            return null;
        }
        return $result;
    }

    public function getClientFromFacebookPageId($facebook_page_id)
    {
        if (!is_string($facebook_page_id)) {
            $facebook_page_id = $this->bigIntToString($facebook_page_id);
        }
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array(
            'client_id',
            'site_id'
        ));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('facebook_page_id', $facebook_page_id);
        $result = $this->mongo_db->get('playbasis_facebook_page_to_client');
        if (!$result) {
            return $result;
        }
        $result = $result[0];
//      unset($result['_id']);
        return $result;
    }

    public function getClientFromHashTag($hashtag)
    {
        assert(is_string($hashtag));
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array('client_id', 'site_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('hashtag', $hashtag);
        $result = $this->mongo_db->get('playbasis_hashtag_to_client');
        if (!$result) {
            return $result;
        }
        $result = $result[0];
        return $result;
    }

    public function getClientFromHost($host)
    {
        assert(is_string($host));
        $this->set_site_mongodb(0);
        $this->mongo_db->select(array(
            'client_id',
            'site_id'
        ));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('host', $host);
        $result = $this->mongo_db->get('playbasis_hosts_to_client');
        if (!$result) {
            return $result;
        }
        $result = $result[0];
//      unset($result['_id']);
        return $result;
    }

    public function getPBPlayerIdFromFacebookId($facebook_id, $client_id, $site_id)
    {
        if (!is_string($facebook_id)) {
            $facebook_id = $this->bigIntToString($facebook_id);
        }
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->where(array(
            'facebook_id' => $facebook_id,
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $result = $this->mongo_db->get('playbasis_player');
        return ($result) ? $result[0]['pb_player_id'] : 0;
    }

    public function getPBPlayerIdFromTwitterId($twitter_id, $client_id, $site_id)
    {
        assert(is_string($twitter_id));
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->where(array(
            'twitter_id' => $twitter_id,
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $result = $this->mongo_db->get('playbasis_player');
        return ($result) ? $result[0]['pb_player_id'] : 0;
    }

    public function getPBPlayerIdFromInstagramId($instagram_id, $client_id, $site_id)
    {
        assert(is_string($instagram_id));
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('pb_player_id'));
        $this->mongo_db->where(array(
            'instagram_id' => $instagram_id,
            'client_id' => $client_id,
            'site_id' => $site_id
        ));
        $result = $this->mongo_db->get('playbasis_player');
        return ($result) ? $result[0]['pb_player_id'] : 0;
    }

    public function saveInstagramFeedData($data)
    {
        $this->set_site_mongodb(0);
        $this->mongo_db->insert('instagram_log', array_merge($data, array(
            'date_added' => new MongoDate(time()),
            'date_modified' => new MongoDate(time())
        )));
    }

    private function formatFacebookPostId($postId, $pageId)
    {
        if (!is_string($postId)) {
            $postId = $this->bigIntToString($postId);
        }
        if (!is_string($pageId)) {
            $pageId = $this->bigIntToString($pageId);
        }
        $postIds = explode("_", $postId);
        if (count($postIds) == 1) {
            $postId = $pageId . '_' . $postId;
        }
        return $postId;
    }

    private function formatFacebookCommentId($commentId, $postId, $pageId)
    {
        if (!is_string($commentId)) {
            $commentId = $this->bigIntToString($commentId);
        }
        if (!is_string($postId)) {
            $postId = $this->bigIntToString($postId);
        }
        if (!is_string($pageId)) {
            $pageId = $this->bigIntToString($pageId);
        }
        $commentIds = explode("_", $commentId);
        if (count($commentIds) == 1) {
            $parentId = formatPostId($postId, $pageId);
            $commentId = $parentId . '_' . $commentId;
        }
        return $commentId;
    }

    private function getFacebookPostData($client_id, $site_id, $postId)
    {
        assert(is_string($postId));
        $result = array();
        try {
            $facebook = $this->getFacebookObject($client_id, $site_id);
            $postData = $facebook->api('/' . $postId);
        } catch (FacebookApiException $e) {
            return $result;
        }
        $result['from_name'] = $postData['from']['name'];
        $result['from_id'] = $postData['from']['id'];
        $result['message'] = $postData['message'];
        return $result;
    }

    private function getFacebookCommentData($client_id, $site_id, $commentId)
    {
        return $this->getFacebookPostData($client_id, $site_id, $commentId);
    }

    private function bigIntToString($number)
    {
//      $numStr = serialize($number);
        $numStr = $number;
        return substr($numStr, 2, -1);
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

//data from a tweet (partial)
{
    "id_str": "309585986453639169",
    "user": {
        "screen_name": "eddieplaybasis",
        "name": "eddie.playbasis",
        "id_str": "1223610780",
        "profile_image_url": "http://a0.twimg.com/profile_images/2189725547/vipul03-light_normal.jpg"
    },
    "text": "#Facebook News Feed Draws More Criticism http://t.co/WvjPpfvWpf"
}

*/
