<?php
function change_status($nick, $status){
	global $I, $U, $db;
	if(empty($nick)){
		return '';
	}elseif($U['status']<=$status || !preg_match('/^[023567\-]$/', $status)){
		return sprintf($I['cantchgstat'], htmlspecialchars($nick));
	}
	$stmt=$db->prepare('SELECT incognito, style FROM ' . PREFIX . 'members WHERE nickname=? AND status<?;');
	$stmt->execute([$nick, $U['status']]);
	if(!$old=$stmt->fetch(PDO::FETCH_NUM)){
		return sprintf($I['cantchgstat'], htmlspecialchars($nick));
	}
	if($_REQUEST['set']==='-'){
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'members WHERE nickname=?;');
		$stmt->execute([$nick]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET status=1, incognito=0 WHERE nickname=?;');
		$stmt->execute([$nick]);
		return sprintf($I['succdel'], style_this(htmlspecialchars($nick), $old[1]));
	}else{
		if($status<5){
			$old[0]=0;
		}
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET status=?, incognito=? WHERE nickname=?;');
		$stmt->execute([$status, $old[0], $nick]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET status=?, incognito=? WHERE nickname=?;');
		$stmt->execute([$status, $old[0], $nick]);
		return sprintf($I['succchg'], style_this(htmlspecialchars($nick), $old[1]));
	}
}

