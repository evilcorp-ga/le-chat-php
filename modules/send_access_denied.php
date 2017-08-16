<?php
function send_access_denied(){
	global $I, $U;
	header('HTTP/1.1 403 Forbidden');
	print_start('access_denied');
	echo "<h1>$I[accessdenied]</h1>".sprintf($I['loggedinas'], style_this(htmlspecialchars($U['nickname']), $U['style'])).'<br>';
	echo form('logout');
	if(!isset($_REQUEST['session'])){
		echo hidden('session', $U['session']);
	}
	echo submit($I['logout'], 'id="exitbutton"')."</form>";
	print_end();
}
