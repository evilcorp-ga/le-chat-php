<?php
function send_admin($arg=''){
	global $I, $U, $db;
	$ga=(int) get_setting('guestaccess');
	print_start('admin');
	$chlist="<select name=\"name[]\" size=\"5\" multiple><option value=\"\">$I[choose]</option>";
	$chlist.="<option value=\"s &amp;\">$I[allguests]</option>";
	$users=[];
	$stmt=$db->query('SELECT nickname, style, status FROM ' . PREFIX . 'sessions WHERE entry!=0 AND status>0 ORDER BY LOWER(nickname);');
	while($user=$stmt->fetch(PDO::FETCH_NUM)){
		$users[]=[htmlspecialchars($user[0]), $user[1], $user[2]];
	}
	foreach($users as $user){
		if($user[2]<$U['status']){
			$chlist.="<option value=\"$user[0]\" style=\"$user[1]\">$user[0]</option>";
		}
	}
	$chlist.='</select>';
	echo "<h2>$I[admfunc]</h2><i>$arg</i><table>";
	if($U['status']>=7){
		thr();
		echo '<tr><td>'.form_target('view', 'setup').submit($I['initgosetup']).'</form></td></tr>';
	}
	thr();
	echo "<tr><td><table id=\"clean\"><tr><th>$I[cleanmsgs]</th><td>";
	echo form('admin', 'clean');
	echo '<table><tr><td><label><input type="radio" name="what" id="room" value="room">';
	echo "$I[room]</label></td><td>&nbsp;</td><td><label><input type=\"radio\" name=\"what\" id=\"choose\" value=\"choose\" checked>";
	echo "$I[selection]</label></td><td>&nbsp;</td></tr><tr><td colspan=\"3\"><label><input type=\"radio\" name=\"what\" id=\"nick\" value=\"nick\">";
	echo "$I[cleannick]</label> <select name=\"nickname\" size=\"1\"><option value=\"\">$I[choose]</option>";
	$stmt=$db->prepare('SELECT poster FROM ' . PREFIX . "messages WHERE delstatus<? AND poster!='' GROUP BY poster;");
	$stmt->execute([$U['status']]);
	while($nick=$stmt->fetch(PDO::FETCH_NUM)){
		echo '<option value="'.htmlspecialchars($nick[0]).'">'.htmlspecialchars($nick[0]).'</option>';
	}
	echo '</select></td><td>';
	echo submit($I['clean'], 'class="delbutton"').'</td></tr></table></form></td></tr></table></td></tr>';
	thr();
	echo '<tr><td><table id="kick"><tr><th>'.sprintf($I['kickchat'], get_setting('kickpenalty')).'</th></tr><tr><td>';
	echo form('admin', 'kick');
	echo "<table><tr><td>$I[kickreason]</td><td><input type=\"text\" name=\"kickmessage\" size=\"30\"></td><td>&nbsp;</td></tr>";
	echo "<tr><td><label><input type=\"checkbox\" name=\"what\" value=\"purge\" id=\"purge\">$I[kickpurge]</label></td><td>$chlist</td><td>";
	echo submit($I['kick']).'</td></tr></table></form></td></tr></table></td></tr>';
	thr();
	echo "<tr><td><table id=\"logout\"><tr><th>$I[logoutinact]</th><td>";
	echo form('admin', 'logout');
	echo "<table><tr><td>$chlist</td><td>";
	echo submit($I['logout']).'</td></tr></table></form></td></tr></table></td></tr>';
	$views=['sessions', 'filter', 'linkfilter'];
	foreach($views as $view){
		thr();
		echo "<tr><td><table id=\"$view\"><tr><th>".$I[$view].'</th><td>';
		echo form('admin', $view);
		echo submit($I['view']).'</form></td></tr></table></td></tr>';
	}
	thr();
	echo "<tr><td><table id=\"topic\"><tr><th>$I[topic]</th><td>";
	echo form('admin', 'topic');
	echo '<table><tr><td><input type="text" name="topic" size="20" value="'.get_setting('topic').'"></td><td>';
	echo submit($I['change']).'</td></tr></table></form></td></tr></table></td></tr>';
	thr();
	echo "<tr><td><table id=\"guestaccess\"><tr><th>$I[guestacc]</th><td>";
	echo form('admin', 'guestaccess');
	echo '<table>';
	echo '<tr><td><select name="guestaccess">';
	echo '<option value="1"';
	if($ga===1){
		echo ' selected';
	}
	echo ">$I[guestallow]</option>";
	echo '<option value="2"';
	if($ga===2){
		echo ' selected';
	}
	echo ">$I[guestwait]</option>";
	echo '<option value="3"';
	if($ga===3){
		echo ' selected';
	}
	echo ">$I[adminallow]</option>";
	echo '<option value="0"';
	if($ga===0){
		echo ' selected';
	}
	echo ">$I[guestdisallow]</option>";
	if($ga===4){
		echo '<option value="4" selected';
		echo ">$I[disablechat]</option>";
	}
	echo '</select></td><td>'.submit($I['change']).'</td></tr></table></form></td></tr></table></td></tr>';
	thr();
	if(get_setting('suguests')){
		echo "<tr><td><table id=\"suguests\"><tr><th>$I[addsuguest]</th><td>";
		echo form('admin', 'superguest');
		echo "<table><tr><td><select name=\"name\" size=\"1\"><option value=\"\">$I[choose]</option>";
		foreach($users as $user){
			if($user[2]==1){
				echo "<option value=\"$user[0]\" style=\"$user[1]\">$user[0]</option>";
			}
		}
		echo '</select></td><td>'.submit($I['register']).'</td></tr></table></form></td></tr></table></td></tr>';
		thr();
	}
	if($U['status']>=7){
		echo "<tr><td><table id=\"status\"><tr><th>$I[admmembers]</th><td>";
		echo form('admin', 'status');
		echo "<table><td><select name=\"name\" size=\"1\"><option value=\"\">$I[choose]</option>";
		$members=[];
		$result=$db->query('SELECT nickname, style, status FROM ' . PREFIX . 'members ORDER BY LOWER(nickname);');
		while($temp=$result->fetch(PDO::FETCH_NUM)){
			$members[]=[htmlspecialchars($temp[0]), $temp[1], $temp[2]];
		}
		foreach($members as $member){
			echo "<option value=\"$member[0]\" style=\"$member[1]\">$member[0]";
			if($member[2]==0){
				echo ' (!)';
			}elseif($member[2]==2){
				echo ' (G)';
			}elseif($member[2]==3){
			}elseif($member[2]==5){
				echo ' (M)';
			}elseif($member[2]==6){
				echo ' (SM)';
			}elseif($member[2]==7){
				echo ' (A)';
			}else{
				echo ' (SA)';
			}
			echo '</option>';
		}
		echo "</select><select name=\"set\" size=\"1\"><option value=\"\">$I[choose]</option><option value=\"-\">$I[memdel]</option><option value=\"0\">$I[memdeny]</option>";
		if(get_setting('suguests')){
			echo "<option value=\"2\">$I[memsuguest]</option>";
		}
		echo "<option value=\"3\">$I[memreg]</option>";
		echo "<option value=\"5\">$I[memmod]</option>";
		echo "<option value=\"6\">$I[memsumod]</option>";
		if($U['status']>=8){
			echo "<option value=\"7\">$I[memadm]</option>";
		}
		echo '</select></td><td>'.submit($I['change']).'</td></tr></table></form></td></tr></table></td></tr>';
		thr();
		echo "<tr><td><table id=\"passreset\"><tr><th>$I[passreset]</th><td>";
		echo form('admin', 'passreset');
		echo "<table><td><select name=\"name\" size=\"1\"><option value=\"\">$I[choose]</option>";
		foreach($members as $member){
			echo "<option value=\"$member[0]\" style=\"$member[1]\">$member[0]</option>";
		}
		echo '</select></td><td><input type="password" name="pass"></td><td>'.submit($I['change']).'</td></tr></table></form></td></tr></table></td></tr>';
		thr();
		echo "<tr><td><table id=\"register\"><tr><th>$I[regguest]</th><td>";
		echo form('admin', 'register');
		echo "<table><tr><td><select name=\"name\" size=\"1\"><option value=\"\">$I[choose]</option>";
		foreach($users as $user){
			if($user[2]==1){
				echo "<option value=\"$user[0]\" style=\"$user[1]\">$user[0]</option>";
			}
		}
		echo '</select></td><td>'.submit($I['register']).'</td></tr></table></form></td></tr></table></td></tr>';
		thr();
		echo "<tr><td><table id=\"regnew\"><tr><th>$I[regmem]</th></tr><tr><td>";
		echo form('admin', 'regnew');
		echo "<table><tr><td>$I[nick]</td><td>&nbsp;</td><td><input type=\"text\" name=\"name\" size=\"20\"></td><td>&nbsp;</td></tr>";
		echo "<tr><td>$I[pass]</td><td>&nbsp;</td><td><input type=\"password\" name=\"pass\" size=\"20\"></td><td>";
		echo submit($I['register']).'</td></tr></table></form></td></tr></table></td></tr>';
		thr();
	}
	echo "</table><br>";
	echo form('admin').submit($I['reload']).'</form>';
	print_end();
}
