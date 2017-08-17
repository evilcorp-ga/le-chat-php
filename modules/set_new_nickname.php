<?php
function set_new_nickname(){
	global $I, $U, $db;
	$_REQUEST['newnickname']=preg_replace('/\s/', '', $_REQUEST['newnickname']);
	if(!valid_nick($_REQUEST['newnickname'])){
		return sprintf($I['invalnick'], get_setting('maxname'), get_setting('nickregex'));
	}
	$stmt=$db->prepare('SELECT id FROM ' . PREFIX . 'sessions WHERE nickname=? UNION SELECT id FROM ' . PREFIX . 'members WHERE nickname=?;');
	$stmt->execute([$_REQUEST['newnickname'], $_REQUEST['newnickname']]);
	if($stmt->fetch(PDO::FETCH_NUM)){
		return $I['nicknametaken'];
	}else{
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET nickname=? WHERE nickname=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET nickname=? WHERE nickname=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'messages SET poster=? WHERE poster=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'messages SET recipient=? WHERE recipient=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'ignored SET ignby=? WHERE ignby=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'ignored SET ign=? WHERE ign=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'inbox SET poster=? WHERE poster=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'notes SET editedby=? WHERE editedby=?;');
		$stmt->execute([$_REQUEST['newnickname'], $U['nickname']]);
		$U['nickname']=$_REQUEST['newnickname'];
	}
	return '';
}

