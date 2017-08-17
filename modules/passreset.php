<?php
function passreset($nick, $pass){
	global $I, $U, $db;
	if(empty($nick)){
		return '';
	}
	$stmt=$db->prepare('SELECT null FROM ' . PREFIX . 'members WHERE nickname=? AND status<?;');
	$stmt->execute([$nick, $U['status']]);
	if($stmt->fetch(PDO::FETCH_ASSOC)){
		$passhash=password_hash($pass, PASSWORD_DEFAULT);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET passhash=? WHERE nickname=?;');
		$stmt->execute([$passhash, $nick]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET passhash=? WHERE nickname=?;');
		$stmt->execute([$passhash, $nick]);
		return sprintf($I['succpassreset'], htmlspecialchars($nick));
	}else{
		return sprintf($I['cantresetpass'], htmlspecialchars($nick));
	}
}

