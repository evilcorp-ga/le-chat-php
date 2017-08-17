<?php
function send_choose_messages(){
	global $I, $U;
	print_start('choose_messages');
	echo form('admin', 'clean');
	echo hidden('what', 'selected').submit($I['delselmes'], 'class="delbutton"').'<br><br>';
	print_messages($U['status']);
	echo '<br>'.submit($I['delselmes'], 'class="delbutton"')."</form>";
	print_end();
}

