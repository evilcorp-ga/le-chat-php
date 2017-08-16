<?php
function print_stylesheet($init=false){
	global $U;
	//default css
	echo '<style type="text/css">';
	echo 'body{background-color:#000000;color:#FFFFFF;font-size:14px;text-align:center;} ';
	echo 'a:visited{color:#B33CB4;} a:active{color:#FF0033;} a:link{color:#0000FF;} #messages{word-wrap:break-word;} ';
	echo 'input,select,textarea{color:#FFFFFF;background-color:#000000;} .messages a img{width:15%} .messages a:hover img{width:35%} ';
	echo '.error{color:#FF0033;text-align:left;} .delbutton{background-color:#660000;} .backbutton{background-color:#004400;} #exitbutton{background-color:#AA0000;} ';
	echo '.setup table table,.admin table table,.profile table table{width:100%;text-align:left} ';
	echo '.alogin table,.init table,.destroy_chat table,.delete_account table,.sessions table,.filter table,.linkfilter table,.notes table,.approve_waiting table,.del_confirm table,.profile table,.admin table,.backup table,.setup table{margin-left:auto;margin-right:auto;} ';
	echo '.setup table table table,.admin table table table,.profile table table table{border-spacing:0px;margin-left:auto;margin-right:unset;width:unset;} ';
	echo '.setup table table td,.backup #restoresubmit,.backup #backupsubmit,.admin table table td,.profile table table td,.login td+td,.alogin td+td{text-align:right;} ';
	echo '.init td,.backup #restorecheck td,.admin #clean td,.admin #regnew td,.session td,.messages,.inbox,.approve_waiting td,.choose_messages,.greeting,.help,.login td,.alogin td{text-align:left;} ';
	echo '.messages #chatters{max-height:100px;overflow-y:auto;} .messages #chatters a{text-decoration-line:none;} .messages #chatters table{border-spacing:0px;} ';
	echo '.messages #chatters th,.messages #chatters td,.post #firstline{vertical-align:top;} ';
	echo '.approve_waiting #action td:only-child,.help #backcredit,.login td:only-child,.alogin td:only-child,.init td:only-child{text-align:center;} .sessions td,.sessions th,.approve_waiting td,.approve_waiting th{padding: 5px;} ';
	echo '.sessions td td{padding: 1px;} .messages #bottom_link{position:fixed;top:0.5em;right:0.5em;} .messages #top_link{position:fixed;bottom:0.5em;right:0.5em;} ';
	echo '.post table,.controls table,.login table{border-spacing:0px;margin-left:auto;margin-right:auto;} .login table{border:2px solid;} .controls{overflow-y:none;} ';
	echo '#manualrefresh{display:block;position:fixed;text-align:center;left:25%;width:50%;top:-200%;animation:timeout_messages ';
	if(isset($U['refresh'])){
		echo $U['refresh']+20;
	}else{
		echo '160';
	}
	echo 's forwards;z-index:2;background-color:#500000;border:2px solid #ff0000;} ';
	echo '@keyframes timeout_messages{0%{top:-200%;} 99%{top:-200%;} 100%{top:0%;}} ';
	echo '.notes textarea{height:80vh;width:80%;}';
	echo '</style>';
	if($init){
		return;
	}
	$css=get_setting('css');
	$coltxt=get_setting('coltxt');
	if(!empty($U['bgcolour'])){
		$colbg=$U['bgcolour'];
	}else{
		$colbg=get_setting('colbg');
	}
	//overwrite with custom css
	echo "<style type=\"text/css\">body{background-color:#$colbg;color:#$coltxt;} $css</style>";
}
