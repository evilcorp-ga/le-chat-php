<?php
function send_greeting(){
	global $I, $U, $language;
	print_start('greeting', $U['refresh'], "$_SERVER[SCRIPT_NAME]?action=view&session=$U[session]&lang=$language");
	printf("<h1>$I[greetingmsg]</h1>", style_this(htmlspecialchars($U['nickname']), $U['style']));
	printf("<hr><small>$I[entryhelp]</small>", $U['refresh']);
	$rulestxt=get_setting('rulestxt');
	if(!empty($rulestxt)){
		echo "<hr><div id=\"rules\"><h2>$I[rules]</h2>$rulestxt</div>";
	}
	print_end();
}

