<?php
function get_count_mods(){
	global $db;
	$c=$db->query('SELECT COUNT(*) FROM ' . PREFIX . 'sessions WHERE status>=5')->fetch(PDO::FETCH_NUM);
	return $c[0];
}

