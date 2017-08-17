<?php
function valid_regex(&$regex){
	$regex=preg_replace('~(^|[^\\\\])/~', "$1\/u", $regex); // Escape "/" if not yet escaped
	return (@preg_match("/$_REQUEST[match]/u", '') !== false);
}

