<?php
function send_del_confirm(){
	global $I;
	print_start('del_confirm');
	echo "<table><tr><td colspan=\"2\">$I[confirm]</td></tr><tr><td>".form('delete');
	if(isset($_REQUEST['multi'])){
		echo hidden('multi', 'on');
	}
	if(isset($_REQUEST['sendto'])){
		echo hidden('sendto', $_REQUEST['sendto']);
	}
	echo hidden('confirm', 'yes').hidden('what', $_REQUEST['what']).submit($I['yes'], 'class="delbutton"').'</form></td><td>'.form('post');
	if(isset($_REQUEST['multi'])){
		echo hidden('multi', 'on');
	}
	if(isset($_REQUEST['sendto'])){
		echo hidden('sendto', $_REQUEST['sendto']);
	}
	echo submit($I['no'], 'class="backbutton"').'</form></td><tr></table>';
	print_end();
}

