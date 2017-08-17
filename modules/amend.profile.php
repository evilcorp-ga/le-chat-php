<?php
function amend_profile(){
	global $U;
	if(isset($_REQUEST['refresh'])){
		$U['refresh']=$_REQUEST['refresh'];
	}
	if($U['refresh']<5){
		$U['refresh']=5;
	}elseif($U['refresh']>150){
		$U['refresh']=150;
	}
	if(preg_match('/^#([a-f0-9]{6})$/i', $_REQUEST['colour'], $match)){
		$colour=$match[1];
	}else{
		preg_match('/#([0-9a-f]{6})/i', $U['style'], $matches);
		$colour=$matches[1];
	}
	if(preg_match('/^#([a-f0-9]{6})$/i', $_REQUEST['bgcolour'], $match)){
		$U['bgcolour']=$match[1];
	}
	$U['style']="color:#$colour;";
	if($U['status']>=3){
		$F=load_fonts();
		if(isset($F[$_REQUEST['font']])){
			$U['style'].=$F[$_REQUEST['font']];
		}
		if(isset($_REQUEST['small'])){
			$U['style'].='font-size:smaller;';
		}
		if(isset($_REQUEST['italic'])){
			$U['style'].='font-style:italic;';
		}
		if(isset($_REQUEST['bold'])){
			$U['style'].='font-weight:bold;';
		}
	}
	if($U['status']>=5 && isset($_REQUEST['incognito']) && get_setting('incognito')){
		$U['incognito']=1;
	}else{
		$U['incognito']=0;
	}
	if(isset($_REQUEST['tz'])){
		$tzs=timezone_identifiers_list();
		if(in_array($_REQUEST['tz'], $tzs)){
			$U['tz']=$_REQUEST['tz'];
		}
	}
	if(isset($_REQUEST['eninbox']) && $_REQUEST['eninbox']>=0 && $_REQUEST['eninbox']<=5){
		$U['eninbox']=$_REQUEST['eninbox'];
	}
	$bool_settings=['timestamps', 'embed', 'nocache', 'sortupdown', 'hidechatters'];
	foreach($bool_settings as $setting){
		if(isset($_REQUEST[$setting])){
			$U[$setting]=1;
		}else{
			$U[$setting]=0;
		}
	}
}

