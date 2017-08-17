<?php
function kick_chatter($names, $mes, $purge){
	global $U, $db;
	$lonick='';
	$time=60*(get_setting('kickpenalty')-get_setting('guestexpire'))+time();
	$check=$db->prepare('SELECT style, entry FROM ' . PREFIX . 'sessions WHERE nickname=? AND status!=0 AND (status<? OR nickname=?);');
	$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET lastpost=?, status=0, kickmessage=? WHERE nickname=?;');
	$all=false;
	if($names[0]==='s &'){
		$tmp=$db->query('SELECT nickname FROM ' . PREFIX . 'sessions WHERE status=1;');
		$names=[];
		while($name=$tmp->fetch(PDO::FETCH_NUM)){
			$names[]=$name[0];
		}
		$all=true;
	}
	$i=0;
	foreach($names as $name){
		$check->execute([$name, $U['status'], $U['nickname']]);
		if($temp=$check->fetch(PDO::FETCH_ASSOC)){
			$stmt->execute([$time, $mes, $name]);
			if($purge){
				del_all_messages($name, $temp['entry']);
			}
			$lonick.=style_this(htmlspecialchars($name), $temp['style']).', ';
			++$i;
		}
	}
	if($i>0){
		if($all){
			add_system_message(get_setting('msgallkick'));
		}else{
			$lonick=substr($lonick, 0, -2);
			if($i>1){
				add_system_message(sprintf(get_setting('msgmultikick'), $lonick));
			}else{
				add_system_message(sprintf(get_setting('msgkick'), $lonick));
			}
		}
		return true;
	}
	return false;
}

