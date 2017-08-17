<?php
function check_expired(){
	global $I, $U;
	if(!isset($U['session'])){
		setcookie(COOKIENAME, false);
		$_REQUEST['session']='';
		send_error($I['expire']);
	}
}

