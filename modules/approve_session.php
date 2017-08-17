<?php
function approve_session(){
	global $db;
	if(isset($_REQUEST['what'])){
		if($_REQUEST['what']==='allowchecked' && isset($_REQUEST['csid'])){
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET entry=lastpost WHERE nickname=?;');
			foreach($_REQUEST['csid'] as $nick){
				$stmt->execute([$nick]);
			}
		}elseif($_REQUEST['what']==='allowall' && isset($_REQUEST['alls'])){
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET entry=lastpost WHERE nickname=?;');
			foreach($_REQUEST['alls'] as $nick){
				$stmt->execute([$nick]);
			}
		}elseif($_REQUEST['what']==='denychecked' && isset($_REQUEST['csid'])){
			$time=60*(get_setting('kickpenalty')-get_setting('guestexpire'))+time();
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET lastpost=?, status=0, kickmessage=? WHERE nickname=? AND status=1;');
			foreach($_REQUEST['csid'] as $nick){
				$stmt->execute([$time, $_REQUEST['kickmessage'], $nick]);
			}
		}elseif($_REQUEST['what']==='denyall' && isset($_REQUEST['alls'])){
			$time=60*(get_setting('kickpenalty')-get_setting('guestexpire'))+time();
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET lastpost=?, status=0, kickmessage=? WHERE nickname=? AND status=1;');
			foreach($_REQUEST['alls'] as $nick){
				$stmt->execute([$time, $_REQUEST['kickmessage'], $nick]);
			}
		}
	}
}

