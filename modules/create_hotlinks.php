<?php
function create_hotlinks($message){
	//Make hotlinks for URLs, redirect through dereferrer script to prevent session leakage
	// 1. all explicit schemes with whatever xxx://yyyyyyy
	$message=preg_replace('~(^|[^\w"])(\w+://[^\s<>]+)~iu', "$1<<$2>>", $message);
	// 2. valid URLs without scheme:
	$message=preg_replace('~((?:[^\s<>]*:[^\s<>]*@)?[a-z0-9\-]+(?:\.[a-z0-9\-]+)+(?::\d*)?/[^\s<>]*)(?![^<>]*>)~iu', "<<$1>>", $message); // server/path given
	$message=preg_replace('~((?:[^\s<>]*:[^\s<>]*@)?[a-z0-9\-]+(?:\.[a-z0-9\-]+)+:\d+)(?![^<>]*>)~iu', "<<$1>>", $message); // server:port given
	$message=preg_replace('~([^\s<>]*:[^\s<>]*@[a-z0-9\-]+(?:\.[a-z0-9\-]+)+(?::\d+)?)(?![^<>]*>)~iu', "<<$1>>", $message); // au:th@server given
	// 3. likely servers without any hints but not filenames like *.rar zip exe etc.
	$message=preg_replace('~((?:[a-z0-9\-]+\.)*[a-z2-7]{16}\.onion)(?![^<>]*>)~iu', "<<$1>>", $message);// *.onion
	$message=preg_replace('~([a-z0-9\-]+(?:\.[a-z0-9\-]+)+(?:\.(?!rar|zip|exe|gz|7z|bat|doc)[a-z]{2,}))(?=[^a-z0-9\-\.]|$)(?![^<>]*>)~iu', "<<$1>>", $message);// xxx.yyy.zzz
	// Convert every <<....>> into proper links:
	$message=preg_replace_callback('/<<([^<>]+)>>/u',
		function ($matches){
			if(strpos($matches[1], '://')===false){
				return "<a href=\"http://$matches[1]\" target=\"_blank\">$matches[1]</a>";
			}else{
				return "<a href=\"$matches[1]\" target=\"_blank\">$matches[1]</a>";
			}
		}
	, $message);
	return $message;
}

