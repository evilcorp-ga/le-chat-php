<?php
function send_fatal_error($err){
	global $I;
	echo '<!DOCTYPE html><html><head>'.meta_html();
	echo "<title>$I[fatalerror]</title>";
	echo "<style type=\"text/css\">body{background-color:#000000;color:#FF0033;}</style>";
	echo '</head><body>';
	echo "<h2>$I[fatalerror]: $err</h2>";
	print_end();
}

