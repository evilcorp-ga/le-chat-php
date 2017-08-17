<?php
function send_error($err){
	global $I;
	print_start('error');
	echo "<h2>$I[error]: $err</h2>".form_target('_parent', 'login').submit($I['backtologin'], 'class="backbutton"').'</form>';
	print_end();
}

