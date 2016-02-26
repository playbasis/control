<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Small test suite for the Amazon Simple Email Service library
 *
 * @see /application/libraries/Amazon_ses.php
 */
class Test_amazon_ses extends CI_Controller
{

    function index()
    {

        // Load the required libraries
        $this->load->library('unit_test');

        // Make sure we're running in strict test mode
        $this->unit->use_strict(true);

        // Check if the config is loaded correctly
        $this->unit->run($this->amazon_ses->from, 'is_string', 'Configuration file loaded',
            'Since most config variables are protected, we only test $from and assume other config variables are in the same state.');

        // Set from address
        $this->amazon_ses->from(EMAIL_FROM);
        $this->unit->run($this->amazon_ses->from, EMAIL_FROM, 'Set from address', '-');

        // Set invalid from address
        $this->amazon_ses->from('wee@wee.com');
        $this->unit->run($this->amazon_ses->from, 'wee@wee.com', 'Set invalid from address',
            'Because we provided an invalid address, the previously set address is still set.');

        // Set to
        $receipients = 'wee@playbasis.com';
        $this->amazon_ses->to($receipients);
        $this->unit->run($this->amazon_ses->recipients['to'][0], 'wee@playbasis.com', 'Set to address (single)', '-');

        // Set invalid to
        $receipients = ' ';
        $this->amazon_ses->to($receipients);
        $this->unit->run($this->amazon_ses->recipients['to'], 'is_null', 'Set invalid to address (single)', '-');

        // Set to in comma list
        $receipients = 'wee_weerapat@hotmail.com, wee.weerapat@gmail.com';
        $this->amazon_ses->to($receipients);
        $this->unit->run($this->amazon_ses->recipients['to'][1], 'wee_weerapat@hotmail.com',
            'Set to address (comma separeted) (1)', '-');
        $this->unit->run($this->amazon_ses->recipients['to'][2], 'wee.weerapat@gmail.com',
            'Set to address (comma separeted) (2)', '-');

        // Set to in array
        $receipients = array('wee_weerapat@hotmail.com', 'wee.weerapat@gmail.com');
        $this->amazon_ses->to($receipients);
        $this->unit->run($this->amazon_ses->recipients['to'][3], 'wee_weerapat@hotmail.com',
            'Set to address (array) (3)', '-');
        $this->unit->run($this->amazon_ses->recipients['to'][4], 'wee.weerapat@gmail.com', 'Set to address (array) (4)',
            '-');

        // Set subject
        $this->amazon_ses->subject('Subject');
        $this->unit->run($this->amazon_ses->subject, 'Subject', 'Set message subject', '-');

        // Set attachment
        $this->amazon_ses->attachment('file upload on local machine');
        $this->unit->run($this->amazon_ses->attachment, $this->amazon_ses->attachment);

        // Display all results
        echo $this->unit->report();

    }

    function send()
    {

        $this->load->library('parser');
        $data = array(
            'user_left' => 1000,
            'user_count' => 10000,
            'user_limit' => 9000,
            'domain_name_client' => "Wee.com",
        );

        $subject = "Playbasis user limit alert";
        $htmlMessage = $this->parser->parse('limit_user_alert.html', $data, true);

        //$this->amazon_ses->verify_address(EMAIL_FROM);

        $this->amazon_ses->to(array('wee@playbasis.com'));

        $this->amazon_ses->from(EMAIL_FROM, 'Playbasis');

        $this->amazon_ses->subject($subject);

        $this->amazon_ses->message($htmlMessage);

        $this->amazon_ses->message_alt('If you cannot view this email, please visit http://playbasis.com');

        $this->amazon_ses->attachment('C:\Program Files (x86)\Ampps\www\api\images\logo-playbasis.png');
        /*$this->amazon_ses->attachment(array(
            'C:\Program Files (x86)\Ampps\www\api\images\logo-playbasis.png',
            'C:\Program Files (x86)\Ampps\www\api\images\like-glyph.png',
            'C:\Program Files (x86)\Ampps\www\api\images\tweet-glyph.png',
        ));*/
        /*$this->amazon_ses->attachment(array(
            'C:\Program Files (x86)\Ampps\www\api\images\logo-playbasis.png' => 'email-logo-playbasis.png',
            'C:\Program Files (x86)\Ampps\www\api\images\like-glyph.png' => 'a.png',
            'C:\Program Files (x86)\Ampps\www\api\images\tweet-glyph.png' => 'b.png',
        ));*/

        $this->amazon_ses->debug(true);

        $res = $this->amazon_ses->send();

        var_dump($res);
        echo "success";
    }

}