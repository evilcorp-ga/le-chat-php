<?php
function check_kicked(){
	global $I, $U;
	if($U['status']==0){
		setcookie(COOKIENAME, false);
		$_REQUEST['session']='';
		send_error("$I[kicked]<br>$U[kickmessage]");
	}
}

