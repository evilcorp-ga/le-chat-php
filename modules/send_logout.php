<?php
function send_logout(){
	global $I, $U;
	print_start('logout');
	echo '<h1>'.sprintf($I['bye'], style_this(htmlspecialchars($U['nickname']), $U['style'])).'</h1>'.form_target('_parent', 'login').submit($I['backtologin'], 'class="backbutton"').'</form>';
	print_end();
}

