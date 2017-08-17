<?php
function get_nowchatting(){
	global $I, $db;
	parse_sessions();
	$stmt=$db->query('SELECT COUNT(*) FROM ' . PREFIX . 'sessions WHERE entry!=0 AND status>0 AND incognito=0;');
	$count=$stmt->fetch(PDO::FETCH_NUM);
	echo '<div id="chatters">'.sprintf($I['curchat'], $count[0]).'<br>';
	if(!get_setting('hidechatters')){
		$stmt=$db->query('SELECT nickname, style FROM ' . PREFIX . 'sessions WHERE entry!=0 AND status>0 AND incognito=0 ORDER BY status DESC, lastpost DESC;');
		while($user=$stmt->fetch(PDO::FETCH_NUM)){
			echo style_this(htmlspecialchars($user[0]), $user[1]).' &nbsp; ';
		}
	}
	echo '</div>';
}

