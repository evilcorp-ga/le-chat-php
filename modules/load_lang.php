<?php
function load_lang(){
	global $I, $L, $language;
	$L=[
		'bg'	=>'Български',
		'de'	=>'Deutsch',
		'en'	=>'English',
		'es'	=>'Español',
		'fr'	=>'Français',
		'id'	=>'Bahasa Indonesia',
		'ru'	=>'Русский',
		'zh_CN'	=>'简体中文',
	];
	if(isset($_REQUEST['lang']) && isset($L[$_REQUEST['lang']])){
		$language=$_REQUEST['lang'];
		if(!isset($_COOKIE['language']) || $_COOKIE['language']!==$language){
			setcookie('language', $language);
		}
	}elseif(isset($_COOKIE['language']) && isset($L[$_COOKIE['language']])){
		$language=$_COOKIE['language'];
	}else{
		$language=LANG;
		setcookie('language', $language);
	}
	include('lang_en.php'); //always include English
	if($language!=='en'){
		$T=[];
		include("lang_$language.php"); //replace with translation if available
		foreach($T as $name=>$translation){
			$I[$name]=$translation;
		}
	}
}

