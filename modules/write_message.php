<?php
function write_message($message){
	global $db;
	if(MSGENCRYPTED){
		$message['text']=openssl_encrypt($message['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
	}
	$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'messages (postdate, poststatus, poster, recipient, text, delstatus) VALUES (?, ?, ?, ?, ?, ?);');
	$stmt->execute([$message['postdate'], $message['poststatus'], $message['poster'], $message['recipient'], $message['text'], $message['delstatus']]);
	if($message['poststatus']<9 && get_setting('sendmail')){
		$subject='New Chat message';
		$headers='From: '.get_setting('mailsender')."\r\nX-Mailer: PHP/".phpversion()."\r\nContent-Type: text/html; charset=UTF-8\r\n";
		$body='<html><body style="background-color:#'.get_setting('colbg').';color:#'.get_setting('coltxt').";\">$message[text]</body></html>";
		mail(get_setting('mailreceiver'), $subject, $body, $headers);
	}
}

