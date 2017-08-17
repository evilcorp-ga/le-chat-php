<?php
function kill_session(){
	global $U, $db;
	parse_sessions();
	check_expired();
	check_kicked();
	setcookie(COOKIENAME, false);
	$_REQUEST['session']='';
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'sessions WHERE session=?;');
	$stmt->execute([$U['session']]);
	if($U['status']>=3 && !$U['incognito']){
		add_system_message(sprintf(get_setting('msgexit'), style_this(htmlspecialchars($U['nickname']), $U['style'])));
	}
}

