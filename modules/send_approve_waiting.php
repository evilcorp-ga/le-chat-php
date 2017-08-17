<?php
function send_approve_waiting(){
	global $I, $db;
	print_start('approve_waiting');
	echo "<h2>$I[waitingroom]</h2>";
	$result=$db->query('SELECT * FROM ' . PREFIX . 'sessions WHERE entry=0 AND status=1 ORDER BY id LIMIT 100;');
	if($tmp=$result->fetchAll(PDO::FETCH_ASSOC)){
		echo form('admin', 'approve');
		echo '<table>';
		echo "<tr><th>$I[sessnick]</th><th>$I[sessua]</th></tr>";
		foreach($tmp as $temp){
			echo '<tr>'.hidden('alls[]', htmlspecialchars($temp['nickname']));
			echo '<td><label><input type="checkbox" name="csid[]" value="'.htmlspecialchars($temp['nickname']).'">';
			echo style_this(htmlspecialchars($temp['nickname']), $temp['style']).'</label></td>';
			echo "<td>$temp[useragent]</td></tr>";
		}
		echo "</table><br><table id=\"action\"><tr><td><label><input type=\"radio\" name=\"what\" value=\"allowchecked\" id=\"allowchecked\" checked>$I[allowchecked]</label></td>";
		echo "<td><label><input type=\"radio\" name=\"what\" value=\"allowall\" id=\"allowall\">$I[allowall]</label></td>";
		echo "<td><label><input type=\"radio\" name=\"what\" value=\"denychecked\" id=\"denychecked\">$I[denychecked]</label></td>";
		echo "<td><label><input type=\"radio\" name=\"what\" value=\"denyall\" id=\"denyall\">$I[denyall]</label></td></tr><tr><td colspan=\"8\">$I[denymessage] <input type=\"text\" name=\"kickmessage\" size=\"45\"></td>";
		echo '</tr><tr><td colspan="8">'.submit($I['butallowdeny']).'</td></tr></table></form>';
	}else{
		echo "$I[waitempty]<br>";
	}
	echo '<br>'.form('view').submit($I['backtochat'], 'class="backbutton"').'</form>';
	print_end();
}
