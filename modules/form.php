<?php
function form($action, $do=''){
	global $language;
	$form="<form action=\"$_SERVER[SCRIPT_NAME]\" enctype=\"multipart/form-data\" method=\"post\">".hidden('lang', $language).hidden('nc', substr(time(), -6)).hidden('action', $action);
	if(!empty($_REQUEST['session'])){
		$form.=hidden('session', $_REQUEST['session']);
	}
	if($do!==''){
		$form.=hidden('do', $do);
	}
	return $form;
}
