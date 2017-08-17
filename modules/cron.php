<?php
function cron(){
	global $db;
	$time=time();
	if(get_setting('nextcron')>$time){
		return;
	}
	update_setting('nextcron', $time+10);
	// delete old sessions
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'sessions WHERE (status<=2 AND lastpost<(?-60*(SELECT value FROM ' . PREFIX . "settings WHERE setting='guestexpire'))) OR (status>2 AND lastpost<(?-60*(SELECT value FROM " . PREFIX . "settings WHERE setting='memberexpire')));");
	$stmt->execute([$time, $time]);
	// delete old messages
	$limit=get_setting('messagelimit');
	$stmt=$db->query('SELECT id FROM ' . PREFIX . "messages WHERE poststatus=1 ORDER BY id DESC LIMIT 1 OFFSET $limit;");
	if($id=$stmt->fetch(PDO::FETCH_NUM)){
		$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'messages WHERE id<=?;');
		$stmt->execute($id);
	}
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'messages WHERE id IN (SELECT * FROM (SELECT id FROM ' . PREFIX . 'messages WHERE postdate<(?-60*(SELECT value FROM ' . PREFIX . "settings WHERE setting='messageexpire'))) AS t);");
	$stmt->execute([$time]);
	// delete expired ignored people
	$result=$db->query('SELECT id FROM ' . PREFIX . 'ignored WHERE ign NOT IN (SELECT nickname FROM ' . PREFIX . 'sessions UNION SELECT nickname FROM ' . PREFIX . 'members UNION SELECT poster FROM ' . PREFIX . 'messages) OR ignby NOT IN (SELECT nickname FROM ' . PREFIX . 'sessions UNION SELECT nickname FROM ' . PREFIX . 'members UNION SELECT poster FROM ' . PREFIX . 'messages);');
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'ignored WHERE id=?;');
	while($tmp=$result->fetch(PDO::FETCH_NUM)){
		$stmt->execute($tmp);
	}
	// delete files that do not belong to any message
	$result=$db->query('SELECT id FROM ' . PREFIX . 'files WHERE postid NOT IN (SELECT id FROM ' . PREFIX . 'messages UNION SELECT postid FROM ' . PREFIX . 'inbox);');
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'files WHERE id=?;');
	while($tmp=$result->fetch(PDO::FETCH_NUM)){
		$stmt->execute($tmp);
	}
	// delete old notes
	$limit=get_setting('numnotes');
	$db->exec('DELETE FROM ' . PREFIX . 'notes WHERE type!=2 AND id NOT IN (SELECT * FROM ( (SELECT id FROM ' . PREFIX . "notes WHERE type=0 ORDER BY id DESC LIMIT $limit) UNION (SELECT id FROM " . PREFIX . "notes WHERE type=1 ORDER BY id DESC LIMIT $limit) ) AS t);");
	$result=$db->query('SELECT editedby, COUNT(*) AS cnt FROM ' . PREFIX . "notes WHERE type=2 GROUP BY editedby HAVING cnt>$limit;");
	$stmt=$db->prepare('DELETE FROM ' . PREFIX . 'notes WHERE type=2 AND editedby=? AND id NOT IN (SELECT * FROM (SELECT id FROM ' . PREFIX . "notes WHERE type=2 AND editedby=? ORDER BY id DESC LIMIT $limit) AS t);");
	while($tmp=$result->fetch(PDO::FETCH_NUM)){
		$stmt->execute([$tmp[0], $tmp[0]]);
	}
}

