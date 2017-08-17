<?php
function logout_chatter($names){
	global $U, $db;
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'sessions WHERE nickname=? AND status<?;');
	if($names[0]==='s &'){
		$tmp=$db->query('SELECT nickname FROM ' . PREFIX . 'sessions WHERE status=1;');
		$names=[];
		while($name=$tmp->fetch(PDO::FETCH_NUM)){
			$names[]=$name[0];
		}
	}
	foreach($names as $name){
		$stmt->execute([$name, $U['status']]);
	}
}

