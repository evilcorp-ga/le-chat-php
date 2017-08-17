<?php
function prepare_message_print(&$message, $removeEmbed){
	if(MSGENCRYPTED){
		$message['text']=openssl_decrypt($message['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
	}
	if($removeEmbed){
		$message['text']=preg_replace_callback('/<img src="([^"]+)"><\/a>/u',
			function ($matched){
				return "$matched[1]</a>";
			}
		, $message['text']);
	}
}

