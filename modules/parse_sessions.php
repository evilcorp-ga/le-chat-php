<?php
function parse_sessions(){
	global $U, $db;
	// look for our session
	if(isset($_REQUEST['session'])){
		$stmt=$db->prepare('SELECT * FROM ' . PREFIX . 'sessions WHERE session=?;');
		$stmt->execute([$_REQUEST['session']]);
		if($tmp=$stmt->fetch(PDO::FETCH_ASSOC)){
			$U=$tmp;
		}
	}
	set_default_tz();
}

