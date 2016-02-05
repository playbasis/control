<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';
class Client extends REST2_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('client_model');
		$this->load->model('player_model');
		$this->load->model('point_model');
		$this->load->model('badge_model');
		$this->load->model('tool/error', 'error');
		$this->load->model('tool/respond', 'resp');
	}
	public function test_get()
	{
		echo '<pre>';
		$credential = array(
			'key' => 'abc',
			'secret' => 'abcde'
		);
		$token = $this->auth_model->getApiInfo($credential);
		echo '<br>getRuleSet<br>';
		$result = $this->client_model->getRuleSet(array(
			'client_id' => $token['client_id'],
			'site_id' => $token['site_id']
		));
		print_r($result);
		echo '<br>getActionId<br>';
		$action_id = $this->client_model->getActionId(array(
			'client_id' => $token['client_id'],
			'site_id' => $token['site_id'],
			'action_name' => 'like'
		));
		print_r($action_id);
		echo '<br>getRuleSetByActionId<br>';
		$result = $this->client_model->getRuleSetByClientSite(array(
			'client_id' => $token['client_id'],
			'site_id' => $token['site_id'],
			'action_id' => $action_id
		));
		print_r($result);
		echo '<br>getJigsawProcessor<br>';
		$result = $this->client_model->getJigsawProcessor(new MongoId('51f120906d6cfb64170000b4'), $token['site_id']);
		print_r($result);
		echo '<br>';
		$cl_player_id = '1';
		$pb_player_id = $this->player_model->getPlaybasisId(array_merge($token, array('cl_player_id' => $cl_player_id)));
		$reward_id = $this->point_model->findPoint(array_merge($token, array('reward_name'=>'point')));
		print_r($reward_id);
		$result = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $token['site_id']);
		print_r($result);
		echo '<br>updatePlayerPointReward<br>';
		$result = $this->client_model->updatePlayerPointReward($reward_id, 20, $pb_player_id, $cl_player_id, $token['client_id'], $token['site_id']);
		print_r($result);
		$result = $this->player_model->getPlayerPoint($pb_player_id, $reward_id, $token['site_id']);
		print_r($result);
		$jigsawConfig = array();
		$input = array_merge($token, array(
			'pb_player_id' => $pb_player_id,
			'player_id' => $cl_player_id
		));
		echo '<br>updateCustomReward<br>';
		$result = $this->client_model->updateCustomReward('custom_pts', 20, $input, $jigsawConfig);
		print_r($result);
		echo '<br>';
		print_r($jigsawConfig);
		$badge_id = new MongoId('51f120906d6cfb641700001f');
		echo '<br>current badges<br>';
		$result = $this->player_model->getBadge($pb_player_id, $token['site_id']);
		print_r($result);
		echo '<br>updatePlayerBadge<br>';
		$result = $this->client_model->updateplayerBadge($badge_id, 1, $pb_player_id, $cl_player_id, $token['client_id'], $token['site_id']);
		print_r($result);
		echo '<br>';
		echo '<br>udpated badges<br>';
		$result = $this->player_model->getBadge($pb_player_id, $token['site_id']);
		print_r($result);
		echo '<br>updateExpAndLevel<br>';
		$result = $this->client_model->updateExpAndLevel(10, $pb_player_id, $cl_player_id, array(
			'site_id' => $token['site_id'],
			'client_id' => $token['client_id']));
		print_r($result);
		echo '<br>log<br>';
		$rule_id = new MongoId('51f1b3506d6cfb64170e81cd');
		$jigsaw_id = new MongoId('51f120906d6cfb64170000b4');
		$result = $this->client_model->log(array(
			'pb_player_id' => $pb_player_id,
			'action_id' => $action_id,
			'action_name' => 'like',
			'client_id' => $token['client_id'],
			'site_id' => $token['site_id'],
			'domain_name' => $token['domain_name'],
			'jigsaw_id' => $jigsaw_id,
			'rule_id' => $rule_id
		));
		print_r($result);
		echo '<br>getBadgeById<br>';
		$result = $this->client_model->getBadgeById($badge_id, $token['site_id']);
		print_r($result);
		echo '<br>getBadge (from badge_model)<br>';
		$result = $this->badge_model->getBadge(array(
			'badge_id' => $badge_id,
			'site_id' => $token['site_id'],
			'client_id' => $token['client_id']
		));
		print_r($result);
		echo '</pre>';
	}
}
?>