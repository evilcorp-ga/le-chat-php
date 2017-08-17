<?php
function send_post($rejected=''){
	global $I, $U, $db;
	print_start('post');
	if(!isset($_REQUEST['sendto'])){
		$_REQUEST['sendto']='';
	}
	echo '<table><tr><td>'.form('post');
	echo hidden('postid', substr(time(), -6));
	if(isset($_REQUEST['multi'])){
		echo hidden('multi', 'on');
	}
	echo '<table><tr><td><table><tr id="firstline"><td>'.style_this(htmlspecialchars($U['nickname']), $U['style']).'</td><td>:</td>';
	if(isset($_REQUEST['multi'])){
		echo "<td><textarea name=\"message\" rows=\"3\" cols=\"40\" style=\"$U[style]\" autofocus>$rejected</textarea></td>";
	}else{
		echo "<td><input type=\"text\" name=\"message\" value=\"$rejected\" size=\"40\" style=\"$U[style]\" autofocus></td>";
	}
	echo '<td>'.submit($I['talkto']).'</td><td><select name="sendto" size="1">';
	echo '<option ';
	if($_REQUEST['sendto']==='s *'){
		echo 'selected ';
	}
	echo "value=\"s *\">-$I[toall]-</option>";
	if($U['status']>=3){
		echo '<option ';
		if($_REQUEST['sendto']==='s ?'){
			echo 'selected ';
		}
		echo "value=\"s ?\">-$I[tomem]-</option>";
	}
	if($U['status']>=5){
		echo '<option ';
		if($_REQUEST['sendto']==='s #'){
			echo 'selected ';
		}
		echo "value=\"s #\">-$I[tostaff]-</option>";
	}
	if($U['status']>=6){
		echo '<option ';
		if($_REQUEST['sendto']==='s &'){
			echo 'selected ';
		}
		echo "value=\"s &amp;\">-$I[toadmin]-</option>";
	}
	$disablepm=(bool) get_setting('disablepm');
	if(!$disablepm){
		$users=[];
		$stmt=$db->prepare('SELECT * FROM (SELECT nickname, style, 0 AS offline FROM ' . PREFIX . 'sessions WHERE entry!=0 AND status>0 AND incognito=0 UNION SELECT nickname, style, 1 AS offline FROM ' . PREFIX . 'members WHERE eninbox!=0 AND eninbox<=? AND nickname NOT IN (SELECT nickname FROM ' . PREFIX . 'sessions WHERE incognito=0)) AS t WHERE nickname NOT IN (SELECT ign FROM '. PREFIX . 'ignored WHERE ignby=? UNION SELECT ignby FROM '. PREFIX . 'ignored WHERE ign=?) ORDER BY LOWER(nickname);');
		$stmt->execute([$U['status'], $U['nickname'], $U['nickname']]);
		while($tmp=$stmt->fetch(PDO::FETCH_ASSOC)){
			if($tmp['offline']){
				$users[]=["$tmp[nickname] $I[offline]", $tmp['style'], $tmp['nickname']];
			}else{
				$users[]=[$tmp['nickname'], $tmp['style'], $tmp['nickname']];
			}
		}
		foreach($users as $user){
			if($U['nickname']!==$user[2]){
				echo '<option ';
				if($_REQUEST['sendto']==$user[2]){
					echo 'selected ';
				}
				echo 'value="'.htmlspecialchars($user[2])."\" style=\"$user[1]\">".htmlspecialchars($user[0]).'</option>';
			}
		}
	}
	echo '</select></td>';
	if(get_setting('enfileupload')){
		if(!$disablepm && ($U['status']>=5 || ($U['status']>=3 && get_count_mods()==0 && get_setting('memkick')))){
			echo '</tr></table><table><tr id="secondline">';
		}
		printf("<td><input type=\"file\" name=\"file\"><small>$I[maxsize]</small></td>", get_setting('maxuploadsize'));
	}
	if(!$disablepm && ($U['status']>=5 || ($U['status']>=3 && get_count_mods()==0 && get_setting('memkick')))){
		echo "<td><label><input type=\"checkbox\" name=\"kick\" id=\"kick\" value=\"kick\">$I[kick]</label></td>";
		echo "<td><label><input type=\"checkbox\" name=\"what\" id=\"what\" value=\"purge\" checked>$I[alsopurge]</label></td>";
	}
	echo '</tr></table></td></tr></table></form></td></tr><tr><td><table><tr id="thirdline"><td>'.form('delete');
	if(isset($_REQUEST['multi'])){
		echo hidden('multi', 'on');
	}
	echo hidden('sendto', $_REQUEST['sendto']).hidden('what', 'last');
	echo submit($I['dellast'], 'class="delbutton"').'</form></td><td>'.form('delete');
	if(isset($_REQUEST['multi'])){
		echo hidden('multi', 'on');
	}
	echo hidden('sendto', $_REQUEST['sendto']).hidden('what', 'all');
	echo submit($I['delall'], 'class="delbutton"').'</form></td><td style="width:10px;"></td><td>'.form('post');
	if(isset($_REQUEST['multi'])){
		echo submit($I['switchsingle']);
	}else{
		echo hidden('multi', 'on').submit($I['switchmulti']);
	}
	echo hidden('sendto', $_REQUEST['sendto']).'</form></td>';
	echo '</tr></table></td></tr></table>';
	print_end();
}

