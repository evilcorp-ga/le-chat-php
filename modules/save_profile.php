<?php
function save_profile(){
	global $I, $U, $db;
	amend_profile();
	$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET refresh=?, style=?, bgcolour=?, timestamps=?, embed=?, incognito=?, nocache=?, tz=?, eninbox=?, sortupdown=?, hidechatters=? WHERE session=?;');
	$stmt->execute([$U['refresh'], $U['style'], $U['bgcolour'], $U['timestamps'], $U['embed'], $U['incognito'], $U['nocache'], $U['tz'], $U['eninbox'], $U['sortupdown'], $U['hidechatters'], $U['session']]);
	if($U['status']>=2){
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET refresh=?, bgcolour=?, timestamps=?, embed=?, incognito=?, style=?, nocache=?, tz=?, eninbox=?, sortupdown=?, hidechatters=? WHERE nickname=?;');
		$stmt->execute([$U['refresh'], $U['bgcolour'], $U['timestamps'], $U['embed'], $U['incognito'], $U['style'], $U['nocache'], $U['tz'], $U['eninbox'], $U['sortupdown'], $U['hidechatters'], $U['nickname']]);
	}
	if(!empty($_REQUEST['unignore'])){
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'ignored WHERE ign=? AND ignby=?;');
		$stmt->execute([$_REQUEST['unignore'], $U['nickname']]);
	}
	if(!empty($_REQUEST['ignore'])){
		$stmt=$db->prepare('SELECT null FROM ' . PREFIX . 'messages WHERE poster=? AND poster NOT IN (SELECT ign FROM ' . PREFIX . 'ignored WHERE ignby=?);');
		$stmt->execute([$_REQUEST['ignore'], $U['nickname']]);
		if($U['nickname']!==$_REQUEST['ignore'] && $stmt->fetch(PDO::FETCH_NUM)){
			$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'ignored (ign, ignby) VALUES (?, ?);');
			$stmt->execute([$_REQUEST['ignore'], $U['nickname']]);
		}
	}
	if($U['status']>1 && !empty($_REQUEST['newpass'])){
		if(!valid_pass($_REQUEST['newpass'])){
			return sprintf($I['invalpass'], get_setting('minpass'), get_setting('passregex'));
		}
		if(!isset($_REQUEST['oldpass'])){
			$_REQUEST['oldpass']='';
		}
		if(!isset($_REQUEST['confirmpass'])){
			$_REQUEST['confirmpass']='';
		}
		if($_REQUEST['newpass']!==$_REQUEST['confirmpass']){
			return $I['noconfirm'];
		}else{
			$U['newhash']=password_hash($_REQUEST['newpass'], PASSWORD_DEFAULT);
		}
		if(!password_verify($_REQUEST['oldpass'], $U['passhash'])){
			return $I['wrongpass'];
		}
		$U['passhash']=$U['newhash'];
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET passhash=? WHERE session=?;');
		$stmt->execute([$U['passhash'], $U['session']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET passhash=? WHERE nickname=?;');
		$stmt->execute([$U['passhash'], $U['nickname']]);
	}
	if($U['status']>1 && !empty($_REQUEST['newnickname'])){
		$msg=set_new_nickname();
		if($msg!==''){
			return $msg;
		}
	}
	return $I['succprofile'];
}

