<?php
function manage_filter(){
	global $db, $memcached;
	if(isset($_REQUEST['id'])){
		$reg=0;
		if($tmp=check_filter_match($reg)){
			return $tmp;
		}
		if(isset($_REQUEST['allowinpm']) && $_REQUEST['allowinpm']==1){
			$pm=1;
		}else{
			$pm=0;
		}
		if(isset($_REQUEST['kick']) && $_REQUEST['kick']==1){
			$kick=1;
		}else{
			$kick=0;
		}
		if(isset($_REQUEST['cs']) && $_REQUEST['cs']==1){
			$cs=1;
		}else{
			$cs=0;
		}
		if(preg_match('/^[0-9]+$/', $_REQUEST['id'])){
			if(empty($_REQUEST['match'])){
				$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'filter WHERE id=?;');
				$stmt->execute([$_REQUEST['id']]);
			}else{
				$stmt=$db->prepare('UPDATE ' . PREFIX . 'filter SET filtermatch=?, filterreplace=?, allowinpm=?, regex=?, kick=?, cs=? WHERE id=?;');
				$stmt->execute([$_REQUEST['match'], $_REQUEST['replace'], $pm, $reg, $kick, $cs, $_REQUEST['id']]);
			}
		}elseif($_REQUEST['id']==='+'){
			$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'filter (filtermatch, filterreplace, allowinpm, regex, kick, cs) VALUES (?, ?, ?, ?, ?, ?);');
			$stmt->execute([$_REQUEST['match'], $_REQUEST['replace'], $pm, $reg, $kick, $cs]);
		}
		if(MEMCACHED){
			$memcached->delete(DBNAME . '-' . PREFIX . 'filter');
		}
	}
}
