<?php
function register_guest($status, $nick){
	global $I, $U, $db;
	$stmt=$db->prepare('SELECT style FROM ' . PREFIX . 'members WHERE nickname=?');
	$stmt->execute([$nick]);
	if($tmp=$stmt->fetch(PDO::FETCH_NUM)){
		return sprintf($I['alreadyreged'], style_this(htmlspecialchars($nick), $tmp[0]));
	}
	$stmt=$db->prepare('SELECT * FROM ' . PREFIX . 'sessions WHERE nickname=? AND status=1;');
	$stmt->execute([$nick]);
	if($reg=$stmt->fetch(PDO::FETCH_ASSOC)){
		$reg['status']=$status;
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET status=? WHERE session=?;');
		$stmt->execute([$reg['status'], $reg['session']]);
	}else{
		return sprintf($I['cantreg'], htmlspecialchars($nick));
	}
	$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'members (nickname, passhash, status, refresh, bgcolour, regedby, timestamps, embed, style, incognito, nocache, tz, eninbox, sortupdown, hidechatters, nocache_old) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
	$stmt->execute([$reg['nickname'], $reg['passhash'], $reg['status'], $reg['refresh'], $reg['bgcolour'], $U['nickname'], $reg['timestamps'], $reg['embed'], $reg['style'], $reg['incognito'], $reg['nocache'], $reg['tz'], $reg['eninbox'], $reg['sortupdown'], $reg['hidechatters'], $reg['nocache_old']]);
	if($reg['status']==3){
		add_system_message(sprintf(get_setting('msgmemreg'), style_this(htmlspecialchars($reg['nickname']), $reg['style'])));
	}else{
		add_system_message(sprintf(get_setting('msgsureg'), style_this(htmlspecialchars($reg['nickname']), $reg['style'])));
	}
	return sprintf($I['successreg'], style_this(htmlspecialchars($reg['nickname']), $reg['style']));
}

