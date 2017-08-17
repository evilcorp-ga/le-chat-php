<?php
function send_headers(){
	header('Content-Type: text/html; charset=UTF-8');
	header('Pragma: no-cache');
	header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
	header('Expires: 0');
	header('Referrer-Policy: no-referrer');
	header('Content-Security-Policy: referrer never');
	if($_SERVER['REQUEST_METHOD']==='HEAD'){
		exit; // headers sent, no further processing needed
	}
}

