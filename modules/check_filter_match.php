<?php
function check_filter_match(&$reg){
	global $I;
	$_REQUEST['match']=htmlspecialchars($_REQUEST['match']);
	if(isset($_REQUEST['regex']) && $_REQUEST['regex']==1){
		if(!valid_regex($_REQUEST['match'])){
			return "$I[incorregex]<br>$I[prevmatch]: $_REQUEST[match]";
		}
		$reg=1;
	}else{
		$_REQUEST['match']=preg_replace('/([^\w\d])/u', "\\\\$1", $_REQUEST['match']);
		$reg=0;
	}
	if(mb_strlen($_REQUEST['match'])>255){
		return "$I[matchtoolong]<br>$I[prevmatch]: $_REQUEST[match]";
	}
	return false;
}
