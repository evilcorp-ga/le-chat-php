<?php
function send_update($msg){
	global $I;
	print_start('update');
	echo "<h2>$I[dbupdate]</h2><br>".form('setup').submit($I['initgosetup'])."</form>$msg<br>".credit();
	print_end();
}
