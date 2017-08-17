<?php
function send_download(){
	global $I, $db;
	if(isset($_REQUEST['id'])){
		$stmt=$db->prepare('SELECT filename, type, data FROM ' . PREFIX . 'files WHERE hash=?;');
		$stmt->execute([$_REQUEST['id']]);
		if($data=$stmt->fetch(PDO::FETCH_ASSOC)){
			header("Content-Type: $data[type]");
			header("Content-disposition: filename=\"$data[filename]\"");
			header('Pragma: no-cache');
			header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
			header('Expires: 0');
			echo base64_decode($data['data']);
		}else{
			send_error($I['filenotfound']);
		}
	}else{
		send_error($I['filenotfound']);
	}
}

