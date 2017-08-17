<?php
function validate_input(){
	global $U, $db;
	$inbox=false;
	$maxmessage=get_setting('maxmessage');
	$message=mb_substr($_REQUEST['message'], 0, $maxmessage);
	$rejected=mb_substr($_REQUEST['message'], $maxmessage);
	if($U['postid']===$_REQUEST['postid']){// ignore double post=reload from browser or proxy
		$message='';
	}elseif((time()-$U['lastpost'])<=1){// time between posts too short, reject!
		$rejected=$_REQUEST['message'];
		$message='';
	}
	if(!empty($rejected)){
		$rejected=trim($rejected);
		$rejected=htmlspecialchars($rejected);
	}
	$message=htmlspecialchars($message);
	$message=preg_replace("/(\r?\n|\r\n?)/u", '<br>', $message);
	if(isset($_REQUEST['multi'])){
		$message=preg_replace('/\s*<br>/u', '<br>', $message);
		$message=preg_replace('/<br>(<br>)+/u', '<br><br>', $message);
		$message=preg_replace('/<br><br>\s*$/u', '<br>', $message);
		$message=preg_replace('/^<br>\s*$/u', '', $message);
	}else{
		$message=str_replace('<br>', ' ', $message);
	}
	$message=trim($message);
	$message=preg_replace('/\s+/u', ' ', $message);
	$recipient='';
	if($_REQUEST['sendto']==='s *'){
		$poststatus=1;
		$displaysend=sprintf(get_setting('msgsendall'), style_this(htmlspecialchars($U['nickname']), $U['style']));
	}elseif($_REQUEST['sendto']==='s ?' && $U['status']>=3){
		$poststatus=3;
		$displaysend=sprintf(get_setting('msgsendmem'), style_this(htmlspecialchars($U['nickname']), $U['style']));
	}elseif($_REQUEST['sendto']==='s #' && $U['status']>=5){
		$poststatus=5;
		$displaysend=sprintf(get_setting('msgsendmod'), style_this(htmlspecialchars($U['nickname']), $U['style']));
	}elseif($_REQUEST['sendto']==='s &' && $U['status']>=6){
		$poststatus=6;
		$displaysend=sprintf(get_setting('msgsendadm'), style_this(htmlspecialchars($U['nickname']), $U['style']));
	}else{// known nick in room?
		if(get_setting('disablepm')){
			return;
		}
		$stmt=$db->prepare('SELECT * FROM (SELECT nickname, style, 1 AS inbox FROM ' . PREFIX . 'members WHERE nickname=? AND eninbox!=0 AND eninbox<=? AND nickname NOT IN (SELECT nickname FROM ' . PREFIX . 'sessions) UNION SELECT nickname, style, 0 AS inbox FROM ' . PREFIX . 'sessions WHERE nickname=?) AS t WHERE nickname NOT IN (SELECT ign FROM ' . PREFIX . 'ignored WHERE ignby=? UNION SELECT ignby FROM ' . PREFIX . 'ignored WHERE ign=?);');
		$stmt->execute([$_REQUEST['sendto'], $U['status'], $_REQUEST['sendto'], $U['nickname'], $U['nickname']]);
		if($tmp=$stmt->fetch(PDO::FETCH_ASSOC)){
			$recipient=$_REQUEST['sendto'];
			$poststatus=9;
			$displaysend=sprintf(get_setting('msgsendprv'), style_this(htmlspecialchars($U['nickname']), $U['style']), style_this(htmlspecialchars($recipient), $tmp['style']));
			$inbox=$tmp['inbox'];
		}
		if(empty($recipient)){// nick left already or ignores us
			$message='';
			$rejected='';
			return;
		}
	}
	if($poststatus!==9 && preg_match('~^/me~iu', $message)){
		$displaysend=style_this(htmlspecialchars("$U[nickname] "), $U['style']);
		$message=preg_replace("~^/me\s?~iu", '', $message);
	}
	$message=apply_filter($message, $poststatus, $U['nickname']);
	$message=create_hotlinks($message);
	$message=apply_linkfilter($message);
	if(isset($_FILES['file']) && get_setting('enfileupload')){
		if($_FILES['file']['error']===UPLOAD_ERR_OK && $_FILES['file']['size']<=(1024*get_setting('maxuploadsize'))){
			$hash=sha1_file($_FILES['file']['tmp_name']);
			$name=htmlspecialchars($_FILES['file']['name']);
			$message=sprintf(get_setting('msgattache'), "<a class=\"attachement\" href=\"$_SERVER[SCRIPT_NAME]?action=download&amp;id=$hash\" target=\"_blank\">$name</a>", $message);
		}
	}
	if(add_message($message, $recipient, $U['nickname'], $U['status'], $poststatus, $displaysend, $U['style'])){
		$U['lastpost']=time();
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET lastpost=?, postid=? WHERE session=?;');
		$stmt->execute([$U['lastpost'], $_REQUEST['postid'], $U['session']]);
		$stmt=$db->prepare('SELECT id FROM ' . PREFIX . 'messages WHERE poster=? ORDER BY id DESC LIMIT 1;');
		$stmt->execute([$U['nickname']]);
		$id=$stmt->fetch(PDO::FETCH_NUM);
		if($inbox && $id){
			$newmessage=[
				'postdate'	=>time(),
				'poster'	=>$U['nickname'],
				'recipient'	=>$recipient,
				'text'		=>"<span class=\"usermsg\">$displaysend".style_this($message, $U['style']).'</span>'
			];
			if(MSGENCRYPTED){
				$newmessage['text']=openssl_encrypt($newmessage['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
			}
			$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'inbox (postdate, postid, poster, recipient, text) VALUES(?, ?, ?, ?, ?)');
			$stmt->execute([$newmessage['postdate'], $id[0], $newmessage['poster'], $newmessage['recipient'], $newmessage['text']]);
		}
		if(isset($hash) && $id){
			if(!empty($_FILES['file']['type']) && preg_match('~^[a-z0-9/\-\.\+]*$~i', $_FILES['file']['type'])){
				$type=$_FILES['file']['type'];
			}else{
				$type='application/octet-stream';
			}
			$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'files (postid, hash, filename, type, data) VALUES (?, ?, ?, ?, ?);');
			$stmt->execute([$id[0], $hash, str_replace('"', '\"', $_FILES['file']['name']), $type, base64_encode(file_get_contents($_FILES['file']['tmp_name']))]);
			unlink($_FILES['file']['tmp_name']);
		}
	}
	return $rejected;
}

