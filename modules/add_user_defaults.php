<?php
function add_user_defaults($password){
	global $U;
	$U['refresh']=get_setting('defaultrefresh');
	$U['bgcolour']=get_setting('colbg');
	if(!isset($_REQUEST['colour']) || !preg_match('/^[a-f0-9]{6}$/i', $_REQUEST['colour']) || abs(greyval($_REQUEST['colour'])-greyval(get_setting('colbg')))<75){
		do{
			$colour=sprintf('%06X', mt_rand(0, 16581375));
		}while(abs(greyval($colour)-greyval(get_setting('colbg')))<75);
	}else{
		$colour=$_REQUEST['colour'];
	}
	$U['style']="color:#$colour;";
	$U['timestamps']=get_setting('timestamps');
	$U['embed']=1;
	$U['incognito']=0;
	$U['status']=1;
	$U['nocache']=get_setting('sortupdown');
	if($U['nocache']){
		$U['nocache_old']=0;
	}else{
		$U['nocache_old']=1;
	}
	$U['tz']=get_setting('defaulttz');
	$U['eninbox']=0;
	$U['sortupdown']=get_setting('sortupdown');
	$U['hidechatters']=get_setting('hidechatters');
	$U['passhash']=password_hash($password, PASSWORD_DEFAULT);
}

