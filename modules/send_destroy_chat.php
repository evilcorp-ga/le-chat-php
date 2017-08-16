<?php
function send_destroy_chat(){
	global $I;
	print_start('destroy_chat');
	echo "<table><tr><td colspan=\"2\">$I[confirm]</td></tr><tr><td>";
	echo form_target('_parent', 'setup', 'destroy').hidden('confirm', 'yes').submit($I['yes'], 'class="delbutton"').'</form></td><td>';
	echo form('setup').submit($I['no'], 'class="backbutton"').'</form></td><tr></table>';
	print_end();
}
