<?php
function del_last_message(){
	global $U, $db;
	if($U['status']>1){
		$entry=0;
	}else{
		$entry=$U['entry'];
	}
	$stmt=$db->prepare('SELECT id FROM ' . PREFIX . 'messages WHERE poster=? AND postdate>=? ORDER BY id DESC LIMIT 1;');
	$stmt->execute([$U['nickname'], $entry]);
	if($id=$stmt->fetch(PDO::FETCH_NUM)){
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'messages WHERE id=?;');
		$stmt->execute($id);
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'inbox WHERE postid=?;');
		$stmt->execute($id);
	}
}

