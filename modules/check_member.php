<?php
function check_member($password){
	global $I, $U, $db;
	$stmt=$db->prepare('SELECT * FROM ' . PREFIX . 'members WHERE nickname=?;');
	$stmt->execute([$U['nickname']]);
	if($temp=$stmt->fetch(PDO::FETCH_ASSOC)){
		if($temp['passhash']===md5(sha1(md5($U['nickname'].$password)))){
			// old hashing method, update on the fly
			$temp['passhash']=password_hash($password, PASSWORD_DEFAULT);
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET passhash=? WHERE nickname=?;');
			$stmt->execute([$temp['passhash'], $U['nickname']]);
		}
		if(password_verify($password, $temp['passhash'])){
			$U=$temp;
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET lastlogin=? WHERE nickname=?;');
			$stmt->execute([time(), $U['nickname']]);
			return true;
		}else{
			send_error("$I[regednick]<br>$I[wrongpass]");
		}
	}
	return false;
}

