<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if(!function_exists('db_get_row_array'))
{
	function db_get_row_array($mdl, $table)
	{
		$sql = $mdl->db->get_compiled_select($table);
		$memId = 'sql_' . md5($sql) . ".$table";
		$result = $mdl->memcached_library->get($memId);
		if(!$result)
		{
			$result = $mdl->db->run_compiled_sql($sql);
			$result = $result->row_array();
			$mdl->memcached_library->add($memId, $result);
		}
		return $result;
	}
}
?>