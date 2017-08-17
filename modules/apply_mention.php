<?php
function apply_mention($message){
	return preg_replace_callback('/\@([^\s]+)/iu', function ($matched){
		global $db;
		$nick=htmlspecialchars_decode($matched[1]);
		$rest='';
		for($i=0;$i<=3;++$i){
			//match case-sensitive present nicknames
			$stmt=$db->prepare('SELECT style FROM ' . PREFIX . 'sessions WHERE nickname=?;');
			$stmt->execute([$nick]);
			if($tmp=$stmt->fetch(PDO::FETCH_NUM)){
				return style_this(htmlspecialchars("@$nick"), $tmp[0]).$rest;
			}
			//match case-insensitive present nicknames
			$stmt=$db->prepare('SELECT style FROM ' . PREFIX . 'sessions WHERE LOWER(nickname)=LOWER(?);');
			$stmt->execute([$nick]);
			if($tmp=$stmt->fetch(PDO::FETCH_NUM)){
				return style_this(htmlspecialchars("@$nick"), $tmp[0]).$rest;
			}
			//match case-sensitive members
			$stmt=$db->prepare('SELECT style FROM ' . PREFIX . 'members WHERE nickname=?;');
			$stmt->execute([$nick]);
			if($tmp=$stmt->fetch(PDO::FETCH_NUM)){
				return style_this(htmlspecialchars("@$nick"), $tmp[0]).$rest;
			}
			//match case-insensitive members
			$stmt=$db->prepare('SELECT style FROM ' . PREFIX . 'members WHERE LOWER(nickname)=LOWER(?);');
			$stmt->execute([$nick]);
			if($tmp=$stmt->fetch(PDO::FETCH_NUM)){
				return style_this(htmlspecialchars("@$nick"), $tmp[0]).$rest;
			}
			if(strlen($nick)===1){
				break;
			}
			$rest=mb_substr($nick, -1).$rest;
			$nick=mb_substr($nick, 0, -1);
		}
		return $matched[0];
	}, $message);
}

