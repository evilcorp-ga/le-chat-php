<?php
function create_session($setup, $nickname, $password){
	global $I, $U, $db, $memcached;
	$U['nickname']=preg_replace('/\s/', '', $nickname);
	if(!check_member($password)){
		add_user_defaults($password);
	}
	$U['entry']=$U['lastpost']=time();
	if($setup && $U['status']>=7){
		$U['incognito']=1;
	}
	$captcha=(int) get_setting('captcha');
	if($captcha!==0 && ($U['status']==1 || get_setting('dismemcaptcha')==0)){
		if(!isset($_REQUEST['challenge'])){
			send_error($I['wrongcaptcha']);
		}
		if(!MEMCACHED){
			$stmt=$db->prepare('SELECT code FROM ' . PREFIX . 'captcha WHERE id=?;');
			$stmt->execute([$_REQUEST['challenge']]);
			$stmt->bindColumn(1, $code);
			if(!$stmt->fetch(PDO::FETCH_BOUND)){
				send_error($I['captchaexpire']);
			}
			$time=time();
			$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'captcha WHERE id=? OR time<(?-(SELECT value FROM ' . PREFIX . "settings WHERE setting='captchatime'));");
			$stmt->execute([$_REQUEST['challenge'], $time]);
		}else{
			if(!$code=$memcached->get(DBNAME . '-' . PREFIX . "captcha-$_REQUEST[challenge]")){
				send_error($I['captchaexpire']);
			}
			$memcached->delete(DBNAME . '-' . PREFIX . "captcha-$_REQUEST[challenge]");
		}
		if($_REQUEST['captcha']!==$code){
			if($captcha!==3 || strrev($_REQUEST['captcha'])!==$code){
				send_error($I['wrongcaptcha']);
			}
		}
	}
	if($U['status']==1){
		$ga=(int) get_setting('guestaccess');
		if(!valid_nick($U['nickname'])){
			send_error(sprintf($I['invalnick'], get_setting('maxname'), get_setting('nickregex')));
		}
		if(!valid_pass($password)){
			send_error(sprintf($I['invalpass'], get_setting('minpass'), get_setting('passregex')));
		}
		if($ga===0){
			send_error($I['noguests']);
		}elseif($ga===3){
			$U['entry']=0;
		}
		if(get_setting('englobalpass')!=0 && isset($_REQUEST['globalpass']) && $_REQUEST['globalpass']!=get_setting('globalpass')){
			send_error($I['wrongglobalpass']);
		}
	}
	write_new_session($password);
}

