<?php
function get_setting($setting){
	global $db, $memcached;
	if(!MEMCACHED || !$value=$memcached->get(DBNAME . '-' . PREFIX . "settings-$setting")){
		$stmt=$db->prepare('SELECT value FROM ' . PREFIX . 'settings WHERE setting=?;');
		$stmt->execute([$setting]);
		$stmt->bindColumn(1, $value);
		$stmt->fetch(PDO::FETCH_BOUND);
		if(MEMCACHED){
			$memcached->set(DBNAME . '-' . PREFIX . "settings-$setting", $value);
		}
	}
	return $value;
}

