<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function get_lang($session, $config)
{
    if ($session->userdata('lang')) {
        $lang = $config->item('supported_languages');
        $lang = $lang[$session->userdata('lang')];
    } else {
        $lang = $config->item('supported_languages');
        $lang = $lang[substr($config->item('language'), 0, 2)];
    }
    return $lang;
}