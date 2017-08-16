<?php
function route_setup(){
	global $U;
	if(!valid_admin()){
		send_alogin();
	}
	$C['bool_settings']=['suguests', 'imgembed', 'timestamps', 'trackip', 'memkick', 'forceredirect', 'incognito', 'sendmail', 'modfallback', 'disablepm', 'eninbox', 'enablegreeting', 'sortupdown', 'hidechatters', 'enfileupload', 'personalnotes', 'filtermodkick'];
	$C['colour_settings']=['colbg', 'coltxt'];
	$C['msg_settings']=['msgenter', 'msgexit', 'msgmemreg', 'msgsureg', 'msgkick', 'msgmultikick', 'msgallkick', 'msgclean', 'msgsendall', 'msgsendmem', 'msgsendmod', 'msgsendadm', 'msgsendprv', 'msgattache'];
	$C['number_settings']=['memberexpire', 'guestexpire', 'kickpenalty', 'entrywait', 'captchatime', 'messageexpire', 'messagelimit', 'maxmessage', 'maxname', 'minpass', 'defaultrefresh', 'numnotes', 'maxuploadsize'];
	$C['textarea_settings']=['rulestxt', 'css', 'disabletext'];
	$C['text_settings']=['dateformat', 'captchachars', 'redirect', 'chatname', 'mailsender', 'mailreceiver', 'nickregex', 'passregex', 'externalcss'];
	$C['settings']=array_merge(['guestaccess', 'englobalpass', 'globalpass', 'captcha', 'dismemcaptcha', 'topic', 'guestreg', 'defaulttz'], $C['bool_settings'], $C['colour_settings'], $C['msg_settings'], $C['number_settings'], $C['textarea_settings'], $C['text_settings']); // All settings in the database
	if(!isset($_REQUEST['do'])){
	}elseif($_REQUEST['do']==='save'){
		save_setup($C);
	}elseif($_REQUEST['do']==='backup' && $U['status']==8){
		send_backup($C);
	}elseif($_REQUEST['do']==='restore' && $U['status']==8){
		restore_backup($C);
		send_backup($C);
	}elseif($_REQUEST['do']==='destroy' && $U['status']==8){
		if(isset($_REQUEST['confirm'])){
			destroy_chat($C);
		}else{
			send_destroy_chat();
		}
	}
	send_setup($C);
}
