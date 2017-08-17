<?php
function write_new_session($password){
	global $I, $U, $db;
	$stmt=$db->prepare('SELECT * FROM ' . PREFIX . 'sessions WHERE nickname=?;');
	$stmt->execute([$U['nickname']]);
	if($temp=$stmt->fetch(PDO::FETCH_ASSOC)){
		// check whether alrady logged in
		if(password_verify($password, $temp['passhash'])){
			$U=$temp;
			check_kicked();
			setcookie(COOKIENAME, $U['session']);
		}else{
			send_error("$I[userloggedin]<br>$I[wrongpass]");
		}
	}else{
		// create new session
		$stmt=$db->prepare('SELECT null FROM ' . PREFIX . 'sessions WHERE session=?;');
		do{
			if(function_exists('random_bytes')){
				$U['session']=bin2hex(random_bytes(16));
			}else{
				$U['session']=md5(uniqid($U['nickname'], true).mt_rand());
			}
			$stmt->execute([$U['session']]);
		}while($stmt->fetch(PDO::FETCH_NUM)); // check for hash collision
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$useragent=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
		}else{
			$useragent='';
		}
		if(get_setting('trackip')){
			$ip=$_SERVER['REMOTE_ADDR'];
		}else{
			$ip='';
		}
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'sessions (session, nickname, status, refresh, style, lastpost, passhash, useragent, bgcolour, entry, timestamps, embed, incognito, ip, nocache, tz, eninbox, sortupdown, hidechatters, nocache_old) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute([$U['session'], $U['nickname'], $U['status'], $U['refresh'], $U['style'], $U['lastpost'], $U['passhash'], $useragent, $U['bgcolour'], $U['entry'], $U['timestamps'], $U['embed'], $U['incognito'], $ip, $U['nocache'], $U['tz'], $U['eninbox'], $U['sortupdown'], $U['hidechatters'], $U['nocache_old']]);
		setcookie(COOKIENAME, $U['session']);
		if($U['status']>=3 && !$U['incognito']){
			add_system_message(sprintf(get_setting('msgenter'), style_this(htmlspecialchars($U['nickname']), $U['style'])));
		}
	}
}

