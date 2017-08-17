<?php
function apply_linkfilter($message){
	$filters=get_linkfilters();
	foreach($filters as $filter){
		$message=preg_replace_callback("/<a href=\"([^\"]+)\" target=\"_blank\">(.*?(?=<\/a>))<\/a>/iu",
			function ($matched) use(&$filter){
				return "<a href=\"$matched[1]\" target=\"_blank\">".preg_replace("/$filter[match]/iu", $filter['replace'], $matched[2]).'</a>';
			}
		, $message);
	}
	$redirect=get_setting('redirect');
	if(get_setting('imgembed')){
		$message=preg_replace_callback('/\[img\]\s?<a href="([^"]+)" target="_blank">(.*?(?=<\/a>))<\/a>/iu',
			function ($matched){
				return str_ireplace('[/img]', '', "<br><a href=\"$matched[1]\" target=\"_blank\"><img src=\"$matched[1]\"></a><br>");
			}
		, $message);
	}
	if(empty($redirect)){
		$redirect="$_SERVER[SCRIPT_NAME]?action=redirect&amp;url=";
	}
	if(get_setting('forceredirect')){
		$message=preg_replace_callback('/<a href="([^"]+)" target="_blank">(.*?(?=<\/a>))<\/a>/u',
			function ($matched) use($redirect){
				return "<a href=\"$redirect".rawurlencode($matched[1])."\" target=\"_blank\">$matched[2]</a>";
			}
		, $message);
	}elseif(preg_match_all('/<a href="([^"]+)" target="_blank">(.*?(?=<\/a>))<\/a>/u', $message, $matches)){
		foreach($matches[1] as $match){
			if(!preg_match('~^http(s)?://~u', $match)){
				$message=preg_replace_callback('/<a href="('.preg_quote($match, '/').')\" target=\"_blank\">(.*?(?=<\/a>))<\/a>/u',
					function ($matched) use($redirect){
						return "<a href=\"$redirect".rawurlencode($matched[1])."\" target=\"_blank\">$matched[2]</a>";
					}
				, $message);
			}
		}
	}
	return $message;
}

