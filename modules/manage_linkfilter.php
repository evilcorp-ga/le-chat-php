<?php
function manage_linkfilter(){
	global $db, $memcached;
	if(isset($_REQUEST['id'])){
		$reg=0;
		if($tmp=check_filter_match($reg)){
			return $tmp;
		}
		if(preg_match('/^[0-9]+$/', $_REQUEST['id'])){
			if(empty($_REQUEST['match'])){
				$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'linkfilter WHERE id=?;');
				$stmt->execute([$_REQUEST['id']]);
			}else{
				$stmt=$db->prepare('UPDATE ' . PREFIX . 'linkfilter SET filtermatch=?, filterreplace=?, regex=? WHERE id=?;');
				$stmt->execute([$_REQUEST['match'], $_REQUEST['replace'], $reg, $_REQUEST['id']]);
			}
		}elseif($_REQUEST['id']==='+'){
			$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'linkfilter (filtermatch, filterreplace, regex) VALUES (?, ?, ?);');
			$stmt->execute([$_REQUEST['match'], $_REQUEST['replace'], $reg]);
		}
		if(MEMCACHED){
			$memcached->delete(DBNAME . '-' . PREFIX . 'linkfilter');
		}
	}
}
