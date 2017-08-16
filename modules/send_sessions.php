<?php
function send_sessions(){
	global $I, $U, $db;
	$stmt=$db->prepare('SELECT nickname, style, lastpost, status, useragent, ip FROM ' . PREFIX . 'sessions WHERE entry!=0 AND (incognito=0 OR status<? OR nickname=?) ORDER BY status DESC, lastpost DESC;');
	$stmt->execute([$U['status'], $U['nickname']]);
	if(!$lines=$stmt->fetchAll(PDO::FETCH_ASSOC)){
		$lines=[];
	}
	print_start('sessions');
	echo "<h1>$I[sessact]</h1><table>";
	echo "<tr><th>$I[sessnick]</th><th>$I[sesstimeout]</th><th>$I[sessua]</th>";
	$trackip=(bool) get_setting('trackip');
	$memexpire=(int) get_setting('memberexpire');
	$guestexpire=(int) get_setting('guestexpire');
	if($trackip) echo "<th>$I[sesip]</th>";
	echo "<th>$I[actions]</th></tr>";
	foreach($lines as $temp){
		if($temp['status']==0){
			$s=' (K)';
		}elseif($temp['status']<=2){
			$s=' (G)';
		}elseif($temp['status']==3){
			$s='';
		}elseif($temp['status']==5){
			$s=' (M)';
		}elseif($temp['status']==6){
			$s=' (SM)';
		}elseif($temp['status']==7){
			$s=' (A)';
		}else{
			$s=' (SA)';
		}
		echo '<tr><td class="nickname">'.style_this(htmlspecialchars($temp['nickname']).$s, $temp['style']).'</td><td class="timeout">';
		if($temp['status']>2){
			get_timeout($temp['lastpost'], $memexpire);
		}else{
			get_timeout($temp['lastpost'], $guestexpire);
		}
		echo '</td>';
		if($U['status']>$temp['status'] || $U['nickname']===$temp['nickname']){
			echo "<td class=\"ua\">$temp[useragent]</td>";
			if($trackip){
				echo "<td class=\"ip\">$temp[ip]</td>";
			}
			echo '<td class="action">';
			if($temp['nickname']!==$U['nickname']){
				echo '<table><tr>';
				if($temp['status']!=0){
					echo '<td>';
					echo form('admin', 'sessions');
					echo hidden('kick', '1').hidden('nick', htmlspecialchars($temp['nickname'])).submit($I['kick']).'</form>';
					echo '</td>';
				}
				echo '<td>';
				echo form('admin', 'sessions');
				echo hidden('logout', '1').hidden('nick', htmlspecialchars($temp['nickname'])).submit($temp['status']==0 ? $I['unban'] : $I['logout']).'</form>';
				echo '</td></tr></table>';
			}else{
				echo '-';
			}
			echo '</td></tr>';
		}else{
			echo '<td class="ua">-</td>';
			if($trackip){
				echo '<td class="ip">-</td>';
			}
			echo '<td class="action">-</td></tr>';
		}
	}
	echo "</table><br>";
	echo form('admin', 'sessions').submit($I['reload']).'</form>';
	print_end();
}
