<?php
function send_chat_disabled(){
	print_start('disabled');
	echo get_setting('disabletext');
	print_end();
}

