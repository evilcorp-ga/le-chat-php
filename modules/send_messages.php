<?php
function send_messages(){
	global $I, $U, $language;
	if($U['nocache']){
		$nocache='&nc='.substr(time(), -6);
	}else{
		$nocache='';
	}
	if($U['sortupdown']){
		$sort='#bottom';
	}else{
		$sort='';
	}
	print_start('messages', $U['refresh'], "$_SERVER[SCRIPT_NAME]?action=view&session=$U[session]&lang=$language$nocache$sort");
	echo '<a id="top"></a>';
	echo "<a id=\"bottom_link\" href=\"#bottom\">$I[bottom]</a>";
	echo "<div id=\"manualrefresh\"><br>$I[manualrefresh]<br>".form('view').submit($I['reload']).'</form><br></div>';
	if(!$U['sortupdown']){
		echo '<div id="topic">';
		echo get_setting('topic');
		echo '</div>';
		print_chatters();
		print_notifications();
		print_messages();
	}else{
		print_messages();
		print_notifications();
		print_chatters();
		echo '<div id="topic">';
		echo get_setting('topic');
		echo '</div>';
	}
	echo "<a id=\"bottom\"></a><a id=\"top_link\" href=\"#top\">$I[top]</a>";
	print_end();
}
