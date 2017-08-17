<?php
function delete_account(){
	global $U, $db;
	if($U['status']<8){
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET status=1, incognito=0 WHERE nickname=?;');
		$stmt->execute([$U['nickname']]);
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'members WHERE nickname=?;');
		$stmt->execute([$U['nickname']]);
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'inbox WHERE recipient=?;');
		$stmt->execute([$U['nickname']]);
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'notes WHERE type=2 AND editedby=?;');
		$stmt->execute([$U['nickname']]);
		$U['status']=1;
	}
}

