<?php
/*
Plugin Name: Playbasis
Plugin URI: http://www.playbasis.com
Description: A plugin to allow Playbasis API to run on Wordpress site
Version: 0.1
Author: Playbasis
Author URI: http://www.playbasis.com
License: GPL2
*/
require 'pblib/playbasis.php';

class PlaybasisWP
{
	public $pb = null;
	const api_key = 'abc';
	const api_secret = 'abcde';
	const commentAction = 'comment';
	const userIdPrefix = 'wpusr_';
	const userNamePrefix = 'wpusr_';
	const defaultProfileImage = 'https://www.pbapp.net/images/default_profile.jpg';
	const defaultLastName = 'wordpress';

	public function __construct()
	{
		$this->pb = new Playbasis();
	}

	function auth()
	{
		$result = $this->pb->auth(self::api_key, self::api_secret);
		assert($result);
	}

	function comment($commentId, $approvalStatus)
	{
		$commentData = get_comment( $commentId, ARRAY_A );
		$userId = $commentData['user_id'];
		if(!$userId)
			return;
		$this->pb->rule($this->getPlaybasisUserId($userId), self::commentAction);
	}

	function login($user_login, $user)
	{
		$userId = $user->ID;
		$result = $this->pb->login($this->getPlaybasisUserId($userId));
		if(!$result['success'] && $result['error_code'] == '0200') //user doesn't exist
		{
			//register and login again
			$this->register($userId);
			$result = $this->pb->login($this->getPlaybasisUserId($userId));
		}
	}

	function logout()
	{
		$user = wp_get_current_user();
		if (!($user instanceof WP_User))
			return;
		$userId = $user->ID;
		$this->pb->logout($this->getPlaybasisUserId($userId));
	}

	function register($userId)
	{
		$user = get_userdata($userId);
		$username = $user->user_login;
		$email = $user->user_email;
		$result = $this->pb->register(
			$this->getPlaybasisUserId($userId), 
			self::userNamePrefix . $username, 
			$email, 
			self::defaultProfileImage, 
			array(
				'first_name' => $username,
				'last_name' => self::defaultLastName
			)
		);
	}

	private function getPlaybasisUserId($userId)
	{
		return self::userIdPrefix . $userId;
	}
}

$playbasisWP = new PlaybasisWP();

add_action('init', array($playbasisWP, 'auth'));
add_action('comment_post', array($playbasisWP, 'comment'), 10,2);
add_action('wp_login', array($playbasisWP, 'login'), 10,2);
add_action('wp_logout', array($playbasisWP, 'logout'));
add_action('user_register', array($playbasisWP, 'register'));

?>