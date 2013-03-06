<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Playbasis extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		$this->load->model('social_model');
	}

	public function test(){
		// /$data['base_url'] = base_url();
		$this->load->view('playbasis/apitest');
	}
	
	public function fb(){
		$this->load->view('playbasis/fb');

		$this->social_model->sendFacebookNotification('802465011', 'notified at ' . date('Y-m-d H:i:s'), '');

		/*////// Test handling signed_request ///////
		
		if ($_REQUEST) {
			$signed_request = $_REQUEST['signed_request'];
			$signed_obj = $this->social_model->parse_signed_request($signed_request);

			echo 'signed request: ' . json_encode($signed_obj);
			
			if(!isset($signed_obj['user_id'])){				
				?>
				<script>
					window.top.location = '<?php echo $this->social_model->get_oauth_url();?>';
				</script>			
				<?php
			}
			else {
				echo 'authorized';
			}			
		} else {
			echo '$_REQUEST is empty';
		}
		*/
	}
}
?>