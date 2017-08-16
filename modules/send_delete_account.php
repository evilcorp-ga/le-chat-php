<?php
function send_delete_account(){
	global $I;
	print_start('delete_account');
	echo "<table><tr><td colspan=\"2\">$I[confirm]</td></tr><tr><td>";
	echo form('profile', 'delete').hidden('confirm', 'yes').submit($I['yes'], 'class="delbutton"').'</form></td><td>';
	echo form('profile').submit($I['no'], 'class="backbutton"').'</form></td><tr></table>';
	print_end();
}
