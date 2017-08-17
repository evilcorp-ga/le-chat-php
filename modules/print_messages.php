<?php
function print_messages($delstatus=0){
	global $U, $db;
	$dateformat=get_setting('dateformat');
	if(!$U['embed'] && get_setting('imgembed')){
		$removeEmbed=true;
	}else{
		$removeEmbed=false;
	}
	if($U['timestamps'] && !empty($dateformat)){
		$timestamps=true;
	}else{
		$timestamps=false;
	}
	if($U['sortupdown']){
		$direction='ASC';
	}else{
		$direction='DESC';
	}
	if($U['status']>1){
		$entry=0;
	}else{
		$entry=$U['entry'];
	}
	echo '<div id="messages">';
	if($delstatus>0){
		$stmt=$db->prepare('SELECT postdate, id, text FROM ' . PREFIX . 'messages WHERE '.
		"(poststatus<? AND delstatus<?) OR ((poster=? OR recipient=?) AND postdate>=?) ORDER BY id $direction;");
		$stmt->execute([$U['status'], $delstatus, $U['nickname'], $U['nickname'], $entry]);
		while($message=$stmt->fetch(PDO::FETCH_ASSOC)){
			prepare_message_print($message, $removeEmbed);
			echo "<div class=\"msg\"><label><input type=\"checkbox\" name=\"mid[]\" value=\"$message[id]\">";
			if($timestamps){
				echo ' <small>'.date($dateformat, $message['postdate']).' - </small>';
			}
			echo " $message[text]</label></div>";
		}
	}else{
		$stmt=$db->prepare('SELECT id, postdate, text FROM ' . PREFIX . 'messages WHERE (poststatus<=? OR '.
		'(poststatus=9 AND ( (poster=? AND recipient NOT IN (SELECT ign FROM ' . PREFIX . 'ignored WHERE ignby=?) ) OR recipient=?) AND postdate>=?)'.
		') AND poster NOT IN (SELECT ign FROM ' . PREFIX . "ignored WHERE ignby=?) ORDER BY id $direction;");
		$stmt->execute([$U['status'], $U['nickname'], $U['nickname'], $U['nickname'], $entry, $U['nickname']]);
		while($message=$stmt->fetch(PDO::FETCH_ASSOC)){
			prepare_message_print($message, $removeEmbed);
			echo '<div class="msg">';
			if($timestamps){
				echo '<small>'.date($dateformat, $message['postdate']).' - </small>';
			}
			echo "$message[text]</div>";
		}
	}
	echo '</div>';
}

