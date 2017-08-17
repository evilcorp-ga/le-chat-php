<?php
function del_all_messages($nick, $entry){
	global $db;
	if($nick==''){
		return;
	}
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'messages WHERE poster=? AND postdate>=?;');
	$stmt->execute([$nick, $entry]);
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'inbox WHERE poster=? AND postdate>=?;');
	$stmt->execute([$nick, $entry]);
}

