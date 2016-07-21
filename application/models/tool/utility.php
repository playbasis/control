<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utility extends CI_Model
{
    public function getEventMessage(
        $eventType,
        $amount = 'some',
        $pointName = 'points',
        $badgeName = 'a',
        $newLevel = '',
        $objectiveName = '',
        $goodsName = '',
        $sent_player = null
    ) {
        switch ($eventType) {
            case 'badge':
                return "earned $badgeName badge";
            case 'point':
                return "earned $amount $pointName";
            case 'level':
                return ($newLevel) ? "is now level $newLevel" : 'gained a level';
            case 'login':
                return 'logged in';
            case 'logout':
                return 'logged out';
            case 'objective':
                return 'completed an objective "' . $objectiveName . '"';
            case 'goods':
                return "redeem $goodsName";
            case 'gift':
                return "$sent_player sent gift to you";
            default:
                return 'did a thing';
        }
    }

    public function elapsed_time($key = "default")
    {
        static $last = array();
        $now = microtime(true);
        $ret = null;
        if (!array_key_exists($key, $last)) {
            $last[$key] = null;
        }
        if ($last[$key] != null) {
            $ret = $now - $last[$key];
        }
        $last[$key] = $now;
        return $ret;
    }

    /* http://stackoverflow.com/questions/19083175/generate-random-string-in-php-for-file-name */
    public function random_string($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }

    /* require: $this->load->library('amazon_ses'); */
    public function email($from, $to, $subject, $message, $message_alt = null, $attachments = array())
    {
        $message_response = array();
        if (is_array($to)) {
            foreach ($to as $email) {
                $message_response[] = $this->_email(array(
                    'from' => $from,
                    'to' => trim($email),
                    'subject' => $subject,
                    'message' => $message,
                    'message_alt' => $message_alt,
                    'attachment' => $attachments,
                ));
                sleep(1);
            }
        } else {
            $message_response = $this->_email(array(
                'from' => $from,
                'to' => trim($to),
                'subject' => $subject,
                'message' => $message,
                'message_alt' => $message_alt,
                'attachment' => $attachments,
            ));
        }
        return $message_response;
    }

    /* require: $this->load->library('amazon_ses'); */
    public function email_bcc($from, $bcc, $subject, $message, $message_alt = null, $attachments = array())
    {
        $message_response = array();
        if (is_array($bcc)) {
            foreach ($bcc as $email) {
                $message_response[] = $this->_email(array(
                    'from' => $from,
                    'bcc' => trim($email),
                    'subject' => $subject,
                    'message' => $message,
                    'message_alt' => $message_alt,
                    'attachment' => $attachments,
                ));
                sleep(1);
            }
        } else {
            $message_response = $this->_email(array(
                'from' => $from,
                'bcc' => trim($bcc),
                'subject' => $subject,
                'message' => $message,
                'message_alt' => $message_alt,
                'attachment' => $attachments,
            ));
        }
        return $message_response;
    }

    /* require: $this->load->library('amazon_ses'); */
    public function _email($data)
    {
        if (!is_array($data)) {
            return null;
        } // error
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'from':
                    $this->amazon_ses->from($value);
                    break;
                case 'to':
                    $this->amazon_ses->to($value);
                    break;
                case 'cc':
                    $this->amazon_ses->cc($value);
                    break;
                case 'bcc':
                    $this->amazon_ses->bcc($value);
                    break;
                case 'subject':
                    $this->amazon_ses->subject($value);
                    break;
                case 'message':
                    $this->amazon_ses->message($value);
                    break;
                case 'message_alt':
                    $this->amazon_ses->message_alt($value);
                    break;
                case 'attachment':
                    $this->amazon_ses->attachment($value);
                    break;
                default:
                    break;
            }
        }
        $this->amazon_ses->debug(EMAIL_DEBUG_MODE);
        $response = $this->amazon_ses->send();
        log_message('info', 'response = ' . $response);
        return $response;
    }

    public function replace_template_vars($template, $data)
    {
        foreach (array(
                     'first_name',
                     'last_name',
                     'cl_player_id',
                     'email',
                     'phone_number',
                     'code',
                     'first_name-2',
                     'last_name-2',
                     'cl_player_id-2',
                     'email-2',
                     'phone_number-2',
                     'code-2',
                     'coupon'
                 ) as $var) {
            if (isset($data[$var])) {
                $template = str_replace('{{' . $var . '}}', $data[$var], $template);
            }
        }
        return $template;
    }

    public function var2file($f, $var)
    {
        $meta = stream_get_meta_data($f);
        $uri = $meta['uri'];
        fwrite($f, $var);
        return $uri;
    }

    public function pagination($page, $limit_per_page, $input = array())
    {
        if (!$limit_per_page || $limit_per_page <= 0) {
            return $input;
        }
        $return_array = array();
        $page = $page < 1 ? 1 : $page; // page is based 1
        $start_page_element = ($page - 1) * $limit_per_page;
        $end_page_element = $start_page_element + $limit_per_page;
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                if ($key < $end_page_element && $key >= $start_page_element) {
                    array_push($return_array, $value);
                }
            }
        }
        return $return_array;
    }

    private function findAdjacentChildNode($org_list, $parent_node)
    {
        $result = array();
        foreach ($org_list as $org) {
            if (array_key_exists('parent', $org) && $org['parent'] == $parent_node) {
                array_push($result, $org['_id']);
            }
        }

        if (empty($result)) {
            return null;
        } else {
            return $result;
        }
    }

    public function recurGetChildUnder($nodesData, $parent_node, &$result, &$layer = 0, $num = 0)
    {
        if ($layer == 0) {
            array_push($result, $parent_node);
        } elseif ($num++ <= $layer) {
            array_push($result, $parent_node);
            if ($num > $layer) {
                return $result;
            }
        }

        $nodes = $this->findAdjacentChildNode($nodesData, new MongoId($parent_node));
        if (isset($nodes)) {
            foreach ($nodes as $node) {

                $this->recurGetChildUnder($nodesData, $node, $result, $layer, $num);
            }
        } else {
            return $result;
        }
    }

    public function recurGetChildByLevel($nodesData, $parent_node, &$result, &$layer = 0, $num = 0)
    {
        if ($layer == 0) {
            array_push($result, $parent_node);
        } elseif ($num++ == $layer) {
            array_push($result, $parent_node);
            return $result;
        }

        $nodes = $this->findAdjacentChildNode($nodesData, new MongoId($parent_node));
        if (isset($nodes)) {
            foreach ($nodes as $node) {

                $this->recurGetChildByLevel($nodesData, $node, $result, $layer, $num);
            }
        } else {
            return $result;
        }
    }

    public function request($class, $method, $arg)
    {
        $base_url = $this->config->base_url();
        $cmd = "curl -k '$base_url$class/$method?$arg'";
        if (EXEC_BACKGROUND) {
            $cmd .= ' > /dev/null 2>&1 &';
        }
        exec($cmd, $output, $exit);
        return $exit == 0;
    }
}

?>