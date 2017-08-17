<?php
function valid_admin(){
	global $U;
	if(isset($_REQUEST['session'])){
		parse_sessions();
	}
	if(!isset($U['session']) && isset($_REQUEST['nick']) && isset($_REQUEST['pass'])){
		create_session(true, $_REQUEST['nick'], $_REQUEST['pass']);
	}
	if(isset($U['status'])){
		if($U['status']>=7){
			return true;
		}
		send_access_denied();
	}
	return false;
}

