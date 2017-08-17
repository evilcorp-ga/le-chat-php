<?php
function check_login(){
	global $I, $U, $db;
	$ga=(int) get_setting('guestaccess');
	if(isset($_REQUEST['session'])){
		parse_sessions();
	}
	if(isset($U['session'])){
		check_kicked();
	}elseif(get_setting('englobalpass')==1 && (!isset($_REQUEST['globalpass']) || $_REQUEST['globalpass']!=get_setting('globalpass'))){
		send_error($I['wrongglobalpass']);
	}elseif(!isset($_REQUEST['nick']) || !isset($_REQUEST['pass'])){
		send_login();
	}else{
		if($ga===4){
			send_chat_disabled();
		}
		if(!empty($_REQUEST['regpass']) && $_REQUEST['regpass']!==$_REQUEST['pass']){
			send_error($I['noconfirm']);
		}
		create_session(false, $_REQUEST['nick'], $_REQUEST['pass']);
		if(!empty($_REQUEST['regpass'])){
			$guestreg=(int) get_setting('guestreg');
			if($guestreg===1){
				register_guest(2, $_REQUEST['nick']);
				$U['status']=2;
			}elseif($guestreg===2){
				register_guest(3, $_REQUEST['nick']);
				$U['status']=3;
			}
		}
	}
	if($U['status']==1){
		if($ga===2 || $ga===3){
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET entry=0 WHERE session=?;');
			$stmt->execute([$U['session']]);
			send_waiting_room();
		}
	}
}

