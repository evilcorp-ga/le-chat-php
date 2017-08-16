<?php
function send_init(){
	global $I, $L;
	print_start('init');
	echo "<h2>$I[init]</h2>";
	echo form('init')."<table><tr><td><h3>$I[sulogin]</h3><table>";
	echo "<tr><td>$I[sunick]</td><td><input type=\"text\" name=\"sunick\" size=\"15\"></td></tr>";
	echo "<tr><td>$I[supass]</td><td><input type=\"password\" name=\"supass\" size=\"15\"></td></tr>";
	echo "<tr><td>$I[suconfirm]</td><td><input type=\"password\" name=\"supassc\" size=\"15\"></td></tr>";
	echo '</table></td></tr><tr><td><br>'.submit($I['initbtn']).'</td></tr></table></form>';
	echo "<p id=\"changelang\">$I[changelang]";
	foreach($L as $lang=>$name){
		echo " <a href=\"$_SERVER[SCRIPT_NAME]?action=setup&amp;lang=$lang\">$name</a>";
	}
	echo '</p>'.credit();
	print_end();
}
