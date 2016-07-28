<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mongo_db');
    }

    public function listBlackListEmails($site_id)
    {
        $this->set_site_mongodb($site_id);
        $ret = $this->mongo_db->get('playbasis_email_blacklist');
        $emails = array();
        foreach ($ret as $each) {
            $emails[] = $each['_id'];
        }
        return $emails;
    }

    public function isEmailInBlackList($emails, $site_id = 0)
    {
        $blackList = $this->listBlackListEmails($site_id);
        $banned = array();
        if (is_array($emails)) {
            foreach ($emails as $email) {
                $banned[] = in_array($email, $blackList);
            }
        } else {
            $banned = in_array($emails, $blackList);
        }
        return $banned;
    }

    public function addIntoBlackList($site_id, $email, $bounce_complaint, $type, $sub_type = null, $ref_id = null)
    {
        $mongoDate = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $data['_id'] = $email;
        $data['bounce_complaint'] = $bounce_complaint;
        $data['type'] = $type;
        $data['sub_type'] = $sub_type;
        $data['ref_id'] = $ref_id;
        $data['date_added'] = $mongoDate;
        $data['date_modified'] = $mongoDate;
        return $this->mongo_db->insert('playbasis_email_blacklist', $data);
    }

    public function removeFromBlackList($site_id, $email)
    {
        if (!$email) {
            return false;
        }
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where(array(
            '_id' => $email,
        ));
        return $this->mongo_db->delete('playbasis_email_blacklist');
    }

    public function log(
        $type,
        $client_id,
        $site_id,
        $response,
        $from,
        $to,
        $subject,
        $message,
        $message_alt = null,
        $attachments = array(),
        $cc = null,
        $bcc = null
    ) {
        $mongoDate = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $data = array(
            'type' => $type,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'from' => $from,
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
            'message' => $message,
            'response' => $response,
        );
        if ($message_alt != null) {
            $data['message_alt'] = $message_alt;
        }
        if (count($attachments) > 0) {
            $data['attachments'] = implode(',', $attachments);
        }
        $data['date_added'] = $mongoDate;
        $data['date_modified'] = $mongoDate;
        /* prevent possibly not utf-8 string error */
        foreach (array('subject', 'message', 'message_alt') as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data[$field]);
            }
        }
        return $this->mongo_db->insert('playbasis_email_log', $data);
    }

    public function getTemplateById($site_id, $template_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('_id', new MongoId($template_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get('playbasis_email_to_client');
        return $results ? $results[0] : null;
    }

    public function getTemplateByTemplateId($site_id, $template_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->where('name', $template_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_email_to_client');
        return $results ? $results[0] : null;
    }

    public function listTemplates($site_id, $includes = null, $excludes = null)
    {
        $this->set_site_mongodb($site_id);
        if ($includes) {
            $this->mongo_db->select($includes);
        }
        if ($excludes) {
            $this->mongo_db->select(null, $excludes);
        }
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('status', true);
        $this->mongo_db->where('deleted', false);
        return $this->mongo_db->get('playbasis_email_to_client');
    }

    public function recent($site_id, $email, $since)
    {
        if (!$email) {
            return array();
        }
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('subject', 'date_added'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('to', $email);
        if ($since) {
            $this->mongo_db->where_gt('date_added', new MongoDate($since));
        }
        $this->mongo_db->order_by(array('date_added' => -1));
        return $this->mongo_db->get('playbasis_email_log');
    }

    public function getClientDomain($client_id, $site_id)
    {
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('status', true);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get("playbasis_domain_to_client");

        return $results ? $results[0] : null;
    }

    public function editDomain($data)
    {
        $this->set_site_mongodb($data['site_id']);

        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if(isset($data["email"])) $this->mongo_db->set("email", $data["email"]);
        if(isset($data["verification_token"])) $this->mongo_db->set('verification_token', $data['verification_token']);
        if(isset($data["verification_status"])) $this->mongo_db->set('verification_status', $data['verification_status']);

        $this->mongo_db->set('status', isset($data['status']) ? (bool)$data['status'] : true);
        $this->mongo_db->set("date_modified", new MongoDate(strtotime(date("Y-m-d H:i:s"))));
        $this->mongo_db->update('playbasis_domain_to_client');
        return true;
    }
}

?>