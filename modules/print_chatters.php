<?php
function print_chatters(){
	global $I, $U, $db, $language;
	if(!$U['hidechatters']){
		echo '<div id="chatters"><table><tr>';
		$stmt=$db->prepare('SELECT nickname, style, status FROM ' . PREFIX . 'sessions WHERE entry!=0 AND status>0 AND incognito=0 AND nickname NOT IN (SELECT ign FROM '. PREFIX . 'ignored WHERE ignby=? UNION SELECT ignby FROM '. PREFIX . 'ignored WHERE ign=?) ORDER BY status DESC, lastpost DESC;');
		$stmt->execute([$U['nickname'], $U['nickname']]);
		$nc=substr(time(), -6);
		while($user=$stmt->fetch(PDO::FETCH_NUM)){
			$link="<a href=\"$_SERVER[SCRIPT_NAME]?action=post&amp;session=$U[session]&amp;lang=$language&amp;nc=$nc&amp;sendto=".htmlspecialchars($user[0]).'" target="post">'.style_this(htmlspecialchars($user[0]), $user[1]).'</a>';
			if($user[2]<=2){
				$G[]=$link;
			}else{
				$M[]=$link;
			}
		}
		if(!empty($M)){
			echo "<th>$I[members]:</th><td>&nbsp;</td><td>".implode(' &nbsp; ', $M).'</td>';
			if(!empty($G)){
				echo '<td>&nbsp;&nbsp;</td>';
			}
		}
		if(!empty($G)){
			echo "<th>$I[guests]:</th><td>&nbsp;</td><td>".implode(' &nbsp; ', $G).'</td>';
		}
		echo '</tr></table></div>';
	}
}

