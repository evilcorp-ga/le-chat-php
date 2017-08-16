<?php
function print_start($class='', $ref=0, $url=''){
	global $I;
	if(!empty($url)){
		$url=str_replace('&amp;', '&', $url);// Don't escape "&" in URLs here, it breaks some (older) browsers and js refresh!
		header("Refresh: $ref; URL=$url");
	}
	echo '<!DOCTYPE html><html><head>'.meta_html();
	if(!empty($url)){
		echo "<meta http-equiv=\"Refresh\" content=\"$ref; URL=$url\">";
		$ref+=5;//only use js if browser refresh stopped working
		$ref*=1000;//js uses milliseconds
		echo "<script type=\"text/javascript\">setTimeout(function(){window.location.replace(\"$url\");}, $ref);</script>";
	}
	if($class==='init'){
		echo "<title>$I[init]</title>";
		print_stylesheet(true);
	}else{
		echo '<title>'.get_setting('chatname').'</title>';
		print_stylesheet();
	}
	echo "</head><body class=\"$class\">";
	if($class!=='init' && ($externalcss=get_setting('externalcss'))!=''){
		//external css - in body to make it non-renderblocking
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$externalcss\">";
	}
}
