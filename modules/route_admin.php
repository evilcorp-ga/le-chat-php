<?php
function route_admin(){
	global $U, $db;
	if($U['status']<5){
		send_access_denied();
	}
	if(!isset($_REQUEST['do'])){
	}elseif($_REQUEST['do']==='clean'){
		if($_REQUEST['what']==='choose'){
			send_choose_messages();
		}elseif($_REQUEST['what']==='selected'){
			clean_selected($U['status'], $U['nickname']);
		}elseif($_REQUEST['what']==='room'){
			clean_room();
		}elseif($_REQUEST['what']==='nick'){
			$stmt=$db->prepare('SELECT null FROM ' . PREFIX . 'members WHERE nickname=? AND status>=?;');
			$stmt->execute([$_REQUEST['nickname'], $U['status']]);
			if(!$stmt->fetch(PDO::FETCH_ASSOC)){
				del_all_messages($_REQUEST['nickname'], 0);
			}
		}
	}elseif($_REQUEST['do']==='kick'){
		if(isset($_REQUEST['name'])){
			if(isset($_REQUEST['what']) && $_REQUEST['what']==='purge'){
				kick_chatter($_REQUEST['name'], $_REQUEST['kickmessage'], true);
			}else{
				kick_chatter($_REQUEST['name'], $_REQUEST['kickmessage'], false);
			}
		}
	}elseif($_REQUEST['do']==='logout'){
		if(isset($_REQUEST['name'])){
			logout_chatter($_REQUEST['name']);
		}
	}elseif($_REQUEST['do']==='sessions'){
		if(isset($_REQUEST['kick']) && isset($_REQUEST['nick'])){
			kick_chatter([$_REQUEST['nick']], '', false);
		}elseif(isset($_REQUEST['logout']) && isset($_REQUEST['nick'])){
			logout_chatter([$_REQUEST['nick']], '', false);
		}
		send_sessions();
	}elseif($_REQUEST['do']==='register'){
		return register_guest(3, $_REQUEST['name']);
	}elseif($_REQUEST['do']==='superguest'){
		return register_guest(2, $_REQUEST['name']);
	}elseif($_REQUEST['do']==='status'){
		return change_status($_REQUEST['name'], $_REQUEST['set']);
	}elseif($_REQUEST['do']==='regnew'){
		return register_new($_REQUEST['name'], $_REQUEST['pass']);
	}elseif($_REQUEST['do']==='approve'){
		approve_session();
		send_approve_waiting();
	}elseif($_REQUEST['do']==='guestaccess'){
		if(isset($_REQUEST['guestaccess']) && preg_match('/^[0123]$/', $_REQUEST['guestaccess'])){
			update_setting('guestaccess', $_REQUEST['guestaccess']);
		}
	}elseif($_REQUEST['do']==='filter'){
		send_filter(manage_filter());
	}elseif($_REQUEST['do']==='linkfilter'){
		send_linkfilter(manage_linkfilter());
	}elseif($_REQUEST['do']==='topic'){
		if(isset($_REQUEST['topic'])){
			update_setting('topic', htmlspecialchars($_REQUEST['topic']));
		}
	}elseif($_REQUEST['do']==='passreset'){
		return passreset($_REQUEST['name'], $_REQUEST['pass']);
	}
}
