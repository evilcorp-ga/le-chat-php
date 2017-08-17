<?php
function check_init(){
	global $db;
	return @$db->query('SELECT null FROM ' . PREFIX . 'settings LIMIT 1;');
}

