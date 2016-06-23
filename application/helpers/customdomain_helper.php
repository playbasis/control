<?php
function verify_custom_domain($client_id, $site_id)
{
    $CI = get_instance();
    $CI->load->model('email_model');
    $email = $CI->email_model->getClientDomain($client_id, $site_id);
    if($email) {
        if($email['verification_status']=="Success"){
            return $email['email'];
        }else {
            $email_array = explode("@", $email['email']);
            $domain_name = $email_array[1];

            // check domain's status from amazon ses
            $domain_verification = $CI->amazon_ses->get_identity_verification($domain_name);
            if (isset($domain_verification['VerificationStatus']) && $domain_verification['VerificationStatus'] == "Success") {
                $data = array('client_id'=>$client_id,
                    'site_id'=>$site_id,
                    'verification_token'=>$domain_verification['VerificationToken'],
                    'verification_status'=>$domain_verification['VerificationStatus']);

                $CI->email_model->editDomain($data);
                return $email['email'];
            } else {
                return EMAIL_FROM;
            }
        }
    }else{
        return EMAIL_FROM;
    }
}

?>
