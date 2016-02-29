<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function vsort(&$array, $key, $order = 'asc')
{
    $res = array();
    $sort = array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sort[$ii] = $va[$key];
    }
    if (strtolower($order) == 'asc') {
        asort($sort);
    } else {
        arsort($sort);
    }
    foreach ($sort as $ii => $va) {
        $res[$ii] = $array[$ii];
    }
    $array = $res;
    return $array;
}