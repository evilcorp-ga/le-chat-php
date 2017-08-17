<?php
function valid_nick($nick){
	$len=mb_strlen($nick);
	if($len<1 || $len>get_setting('maxname')){
		return false;
	}
	return preg_match('/'.get_setting('nickregex').'/u', $nick);
}

