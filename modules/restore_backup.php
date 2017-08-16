<?php
function restore_backup($C){
	global $db, $memcached;
	if(!extension_loaded('json')){
		return;
	}
	$code=json_decode($_REQUEST['restore'], true);
	if(isset($_REQUEST['settings'])){
		foreach($C['settings'] as $setting){
			if(isset($code['settings'][$setting])){
				update_setting($setting, $code['settings'][$setting]);
			}
		}
	}
	if(isset($_REQUEST['filter']) && (isset($code['filters']) || isset($code['linkfilters']))){
		$db->exec('DELETE FROM ' . PREFIX . 'filter;');
		$db->exec('DELETE FROM ' . PREFIX . 'linkfilter;');
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'filter (filtermatch, filterreplace, allowinpm, regex, kick, cs) VALUES (?, ?, ?, ?, ?, ?);');
		foreach($code['filters'] as $filter){
			if(!isset($filter['cs'])){
				$filter['cs']=0;
			}
			$stmt->execute([$filter['match'], $filter['replace'], $filter['allowinpm'], $filter['regex'], $filter['kick'], $filter['cs']]);
		}
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'linkfilter (filtermatch, filterreplace, regex) VALUES (?, ?, ?);');
		foreach($code['linkfilters'] as $filter){
			$stmt->execute([$filter['match'], $filter['replace'], $filter['regex']]);
		}
		if(MEMCACHED){
			$memcached->delete(DBNAME . '-' . PREFIX . 'filter');
			$memcached->delete(DBNAME . '-' . PREFIX . 'linkfilter');
		}
	}
	if(isset($_REQUEST['members']) && isset($code['members'])){
		$db->exec('DELETE FROM ' . PREFIX . 'inbox;');
		$db->exec('DELETE FROM ' . PREFIX . 'members;');
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'members (nickname, passhash, status, refresh, bgcolour, regedby, lastlogin, timestamps, embed, incognito, style, nocache, tz, eninbox, sortupdown, hidechatters, nocache_old) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		foreach($code['members'] as $member){
			$new_settings=['nocache', 'tz', 'eninbox', 'sortupdown', 'hidechatters', 'nocache_old'];
			foreach($new_settings as $setting){
				if(!isset($member[$setting])){
					$member[$setting]=0;
				}
			}
			$stmt->execute([$member['nickname'], $member['passhash'], $member['status'], $member['refresh'], $member['bgcolour'], $member['regedby'], $member['lastlogin'], $member['timestamps'], $member['embed'], $member['incognito'], $member['style'], $member['nocache'], $member['tz'], $member['eninbox'], $member['sortupdown'], $member['hidechatters'], $member['nocache_old']]);
		}
	}
	if(isset($_REQUEST['notes']) && isset($code['notes'])){
		$db->exec('DELETE FROM ' . PREFIX . 'notes;');
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'notes (type, lastedited, editedby, text) VALUES (?, ?, ?, ?);');
		foreach($code['notes'] as $note){
			if($note['type']==='admin'){
				$note['type']=0;
			}elseif($note['type']==='staff'){
				$note['type']=1;
			}
			$stmt->execute([$note['type'], $note['lastedited'], $note['editedby'], $note['text']]);
		}
	}
}
