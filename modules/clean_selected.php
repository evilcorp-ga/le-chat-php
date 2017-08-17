<?php
function clean_selected($status, $nick){
	global $db;
	if(isset($_REQUEST['mid'])){
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'messages WHERE id=? AND (poster=? OR recipient=? OR (poststatus<? AND delstatus<?));');
		foreach($_REQUEST['mid'] as $mid){
			$stmt->execute([$mid, $nick, $nick, $status, $status]);
		}
	}
}

