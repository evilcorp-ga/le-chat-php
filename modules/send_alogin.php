<?php
function send_alogin(){
	global $I, $L;
	print_start('alogin');
	echo form('setup').'<table>';
	echo "<tr><td>$I[nick]</td><td><input type=\"text\" name=\"nick\" size=\"15\" autofocus></td></tr>";
	echo "<tr><td>$I[pass]</td><td><input type=\"password\" name=\"pass\" size=\"15\"></td></tr>";
	send_captcha();
	echo '<tr><td colspan="2">'.submit($I['login']).'</td></tr></table></form>';
	echo "<p id=\"changelang\">$I[changelang]";
	foreach($L as $lang=>$name){
		echo " <a href=\"$_SERVER[SCRIPT_NAME]?action=setup&amp;lang=$lang\">$name</a>";
	}
	echo '</p>'.credit();
	print_end();
}
