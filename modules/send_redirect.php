<?php
function send_redirect($url){
	global $I;
	$url=htmlspecialchars_decode(rawurldecode($url));
	preg_match('~^(.*)://~u', $url, $match);
	$url=preg_replace('~^(.*)://~u', '', $url);
	$escaped=htmlspecialchars($url);
	if(isset($match[1]) && ($match[1]==='http' || $match[1]==='https')){
		print_start('redirect', 0, $match[0].$escaped);
		echo "<p>$I[redirectto] <a href=\"$match[0]$escaped\">$match[0]$escaped</a>.</p>";
	}else{
		print_start('redirect');
		if(!isset($match[0])){
			$match[0]='';
		}
		echo "<p>$I[nonhttp] <a href=\"$match[0]$escaped\">$match[0]$escaped</a>.</p>";
		echo "<p>$I[httpredir] <a href=\"http://$escaped\">http://$escaped</a>.</p>";
	}
	print_end();
}
