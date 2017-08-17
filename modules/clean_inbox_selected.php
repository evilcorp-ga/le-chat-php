<?php
function clean_inbox_selected(){
	global $U, $db;
	if(isset($_REQUEST['mid'])){
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'inbox WHERE id=? AND recipient=?;');
		foreach($_REQUEST['mid'] as $mid){
			$stmt->execute([$mid, $U['nickname']]);
		}
	}
}

