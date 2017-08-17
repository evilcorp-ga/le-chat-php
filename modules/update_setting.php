<?php
function update_setting($setting, $value){
	global $db, $memcached;
	$stmt=$db->prepare('UPDATE ' . PREFIX . 'settings SET value=? WHERE setting=?;');
	$stmt->execute([$value, $setting]);
	if(MEMCACHED){
		$memcached->set(DBNAME . '-' . PREFIX . "settings-$setting", $value);
	}
}

