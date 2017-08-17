<?php
function valid_pass($pass){
	if(mb_strlen($pass)<get_setting('minpass')){
		return false;
	}
	return preg_match('/'.get_setting('passregex').'/u', $pass);
}

