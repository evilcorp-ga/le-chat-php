<?php
function save_setup($C){
	global $db;
	//sanity checks and escaping
	foreach($C['msg_settings'] as $setting){
		$_REQUEST[$setting]=htmlspecialchars($_REQUEST[$setting]);
	}
	foreach($C['number_settings'] as $setting){
		settype($_REQUEST[$setting], 'int');
	}
	foreach($C['colour_settings'] as $setting){
		if(preg_match('/^#([a-f0-9]{6})$/i', $_REQUEST[$setting], $match)){
			$_REQUEST[$setting]=$match[1];
		}else{
			unset($_REQUEST[$setting]);
		}
	}
	settype($_REQUEST['guestaccess'], 'int');
	if(!preg_match('/^[01234]$/', $_REQUEST['guestaccess'])){
		unset($_REQUEST['guestaccess']);
	}elseif($_REQUEST['guestaccess']==4){
		$db->exec('DELETE FROM ' . PREFIX . 'sessions WHERE status<7;');
	}
	settype($_REQUEST['englobalpass'], 'int');
	settype($_REQUEST['captcha'], 'int');
	settype($_REQUEST['dismemcaptcha'], 'int');
	settype($_REQUEST['guestreg'], 'int');
	if(isset($_REQUEST['defaulttz'])){
		$tzs=timezone_identifiers_list();
		if(!in_array($_REQUEST['defaulttz'], $tzs)){
			unset($_REQUEST['defualttz']);
		}
	}
	$_REQUEST['rulestxt']=preg_replace("/(\r?\n|\r\n?)/u", '<br>', $_REQUEST['rulestxt']);
	$_REQUEST['chatname']=htmlspecialchars($_REQUEST['chatname']);
	$_REQUEST['redirect']=htmlspecialchars($_REQUEST['redirect']);
	if($_REQUEST['memberexpire']<5){
		$_REQUEST['memberexpire']=5;
	}
		if($_REQUEST['captchatime']<30){
		$_REQUEST['memberexpire']=30;
	}
	if($_REQUEST['defaultrefresh']<5){
		$_REQUEST['defaultrefresh']=5;
	}elseif($_REQUEST['defaultrefresh']>150){
		$_REQUEST['defaultrefresh']=150;
	}
	if($_REQUEST['maxname']<1){
		$_REQUEST['maxname']=1;
	}elseif($_REQUEST['maxname']>50){
		$_REQUEST['maxname']=50;
	}
	if($_REQUEST['maxmessage']<1){
		$_REQUEST['maxmessage']=1;
	}elseif($_REQUEST['maxmessage']>16000){
		$_REQUEST['maxmessage']=16000;
	}
		if($_REQUEST['numnotes']<1){
		$_REQUEST['numnotes']=1;
	}
	if(!valid_regex($_REQUEST['nickregex'])){
		unset($_REQUEST['nickregex']);
	}
	if(!valid_regex($_REQUEST['passregex'])){
		unset($_REQUEST['passregex']);
	}
	//save values
	foreach($C['settings'] as $setting){
		if(isset($_REQUEST[$setting])){
			update_setting($setting, $_REQUEST[$setting]);
		}
	}
}

