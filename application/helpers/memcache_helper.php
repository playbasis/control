<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function _build_memcache_id($sql, $table, $site_id)
{
//    echo $sql;
    return 'sql_' . md5($sql) . ".$table.$site_id.";
}

function _get_memcached_result($mdl, $table, &$sql, &$memId)
{
    $sql = $mdl->site_db()->get_compiled_select($table);
    $memId = _build_memcache_id($sql, $table, $mdl->get_site());
    return $mdl->memcached_library->get($memId);
//    return null;
}

function _count_all_results_memcached_result($mdl, $table, &$sql, &$memId)
{
    $sql = $mdl->site_db()->get_compiled_count_all_results($table);
    $memId = _build_memcache_id($sql, $table, $mdl->get_site());
    return $mdl->memcached_library->get($memId);
//    return null;
}

function db_get_row_array($mdl, $table)
{
    $result = _get_memcached_result($mdl, $table, $sql, $memId);
    if (!$result) {
        $result = $mdl->site_db()->run_compiled_sql($sql);
        $result = $result->row_array();
        $mdl->memcached_library->set($memId, $result);
    }
    return $result;
}

function db_get_result_array($mdl, $table)
{
    $result = _get_memcached_result($mdl, $table, $sql, $memId);
    if (!$result) {
        $result = $mdl->site_db()->run_compiled_sql($sql);
        $result = $result->result_array();
        $mdl->memcached_library->set($memId, $result);
    }
    return $result;
}

function db_count_all_results($mdl, $table)
{
    $result = _count_all_results_memcached_result($mdl, $table, $sql, $memId);
    if (!$result) {
        $result = $mdl->site_db()->run_compiled_sql_count_all_results($sql);
        $mdl->memcached_library->set($memId, $result);
    }
    return $result;
}

?>