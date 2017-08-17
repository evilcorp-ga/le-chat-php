<?php
function print_notifications(){
	global $I, $U, $db;
	echo '<span id="notifications">';
	if($U['status']>=2 && $U['eninbox']!=0){
		$stmt=$db->prepare('SELECT COUNT(*) FROM ' . PREFIX . 'inbox WHERE recipient=?;');
		$stmt->execute([$U['nickname']]);
		$tmp=$stmt->fetch(PDO::FETCH_NUM);
		if($tmp[0]>0){
			echo '<p>'.form('inbox').submit(sprintf($I['inboxmsgs'], $tmp[0])).'</form></p>';
		}
	}
	if($U['status']>=5 && get_setting('guestaccess')==3){
		$result=$db->query('SELECT COUNT(*) FROM ' . PREFIX . 'sessions WHERE entry=0 AND status=1;');
		$temp=$result->fetch(PDO::FETCH_NUM);
		if($temp[0]>0){
			echo '<p>';
			echo form('admin', 'approve');
			echo submit(sprintf($I['approveguests'], $temp[0])).'</form></p>';
		}
	}
	echo '</span>';
}

