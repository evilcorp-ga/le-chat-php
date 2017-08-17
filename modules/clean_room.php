<?php
function clean_room(){
	global $db;
	$db->query('DELETE FROM ' . PREFIX . 'messages;');
	add_system_message(sprintf(get_setting('msgclean'), get_setting('chatname')));
}

