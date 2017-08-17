<?php
function add_message($message, $recipient, $poster, $delstatus, $poststatus, $displaysend, $style){
	global $db;
	if($message===''){
		return false;
	}
	$newmessage=[
		'postdate'	=>time(),
		'poststatus'	=>$poststatus,
		'poster'	=>$poster,
		'recipient'	=>$recipient,
		'text'		=>"<span class=\"usermsg\">$displaysend".style_this($message, $style).'</span>',
		'delstatus'	=>$delstatus
	];
	//prevent posting the same message twice, if no other message was posted in-between.
	$stmt=$db->prepare('SELECT id FROM ' . PREFIX . 'messages WHERE poststatus=? AND poster=? AND recipient=? AND text=? AND id IN (SELECT * FROM (SELECT id FROM ' . PREFIX . 'messages ORDER BY id DESC LIMIT 1) AS t);');
	$stmt->execute([$newmessage['poststatus'], $newmessage['poster'], $newmessage['recipient'], $newmessage['text']]);
	if($stmt->fetch(PDO::FETCH_NUM)){
		return false;
	}
	write_message($newmessage);
	return true;
}

