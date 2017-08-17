<?php
function apply_filter($message, $poststatus, $nickname){
	global $I, $U;
	$message=str_replace('<br>', "\n", $message);
	$message=apply_mention($message);
	$filters=get_filters();
	foreach($filters as $filter){
		if($poststatus!==9 || !$filter['allowinpm']){
			if($filter['cs']){
				$message=preg_replace("/$filter[match]/u", $filter['replace'], $message, -1, $count);
			}else{
				$message=preg_replace("/$filter[match]/iu", $filter['replace'], $message, -1, $count);
			}
		}
		if(isset($count) && $count>0 && $filter['kick'] && ($U['status']<5 || get_setting('filtermodkick'))){
			kick_chatter([$nickname], $filter['replace'], false);
			setcookie(COOKIENAME, false);
			$_REQUEST['session']='';
			send_error("$I[kicked]<br>$filter[replace]");
		}
	}
	$message=str_replace("\n", '<br>', $message);
	return $message;
}

