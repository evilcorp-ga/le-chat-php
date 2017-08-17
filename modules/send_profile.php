<?php
function send_profile($arg=''){
	global $I, $L, $U, $db, $language;
	print_start('profile');
	echo form('profile', 'save')."<h2>$I[profile]</h2><i>$arg</i><table>";
	thr();
	$ignored=[];
	$stmt=$db->prepare('SELECT ign FROM ' . PREFIX . 'ignored WHERE ignby=? ORDER BY LOWER(ign);');
	$stmt->execute([$U['nickname']]);
	while($tmp=$stmt->fetch(PDO::FETCH_ASSOC)){
		$ignored[]=htmlspecialchars($tmp['ign']);
	}
	if(count($ignored)>0){
		echo "<tr><td><table id=\"unignore\"><tr><th>$I[unignore]</th><td>";
		echo "<select name=\"unignore\" size=\"1\"><option value=\"\">$I[choose]</option>";
		foreach($ignored as $ign){
			echo "<option value=\"$ign\">$ign</option>";
		}
		echo '</select></td></tr></table></td></tr>';
		thr();
	}
	echo "<tr><td><table id=\"ignore\"><tr><th>$I[ignore]</th><td>";
	echo "<select name=\"ignore\" size=\"1\"><option value=\"\">$I[choose]</option>";
	$stmt=$db->prepare('SELECT poster, style FROM ' . PREFIX . 'messages INNER JOIN (SELECT nickname, style FROM ' . PREFIX . 'sessions UNION SELECT nickname, style FROM ' . PREFIX . 'members) AS t ON (' .  PREFIX . 'messages.poster=t.nickname) WHERE poster!=? AND poster NOT IN (SELECT ign FROM ' . PREFIX . 'ignored WHERE ignby=?) GROUP BY poster ORDER BY LOWER(poster);');
	$stmt->execute([$U['nickname'], $U['nickname']]);
	while($nick=$stmt->fetch(PDO::FETCH_NUM)){
		echo '<option value="'.htmlspecialchars($nick[0])."\" style=\"$nick[1]\">".htmlspecialchars($nick[0]).'</option>';
	}
	echo '</select></td></tr></table></td></tr>';
	thr();
	echo "<tr><td><table id=\"refresh\"><tr><th>$I[refreshrate]</th><td>";
	echo "<input type=\"number\" name=\"refresh\" size=\"3\" maxlength=\"3\" min=\"5\" max=\"150\" value=\"$U[refresh]\"></td></tr></table></td></tr>";
	thr();
	preg_match('/#([0-9a-f]{6})/i', $U['style'], $matches);
	echo "<tr><td><table id=\"colour\"><tr><th>$I[fontcolour] (<a href=\"$_SERVER[SCRIPT_NAME]?action=colours&amp;session=$U[session]&amp;lang=$language\" target=\"view\">$I[viewexample]</a>)</th><td>";
	echo "<input type=\"color\" value=\"#$matches[1]\" name=\"colour\"></td></tr></table></td></tr>";
	thr();
	echo "<tr><td><table id=\"bgcolour\"><tr><th>$I[bgcolour] (<a href=\"$_SERVER[SCRIPT_NAME]?action=colours&amp;session=$U[session]&amp;lang=$language\" target=\"view\">$I[viewexample]</a>)</th><td>";
	echo "<input type=\"color\" value=\"#$U[bgcolour]\" name=\"bgcolour\"></td></tr></table></td></tr>";
	thr();
	if($U['status']>=3){
		echo "<tr><td><table id=\"font\"><tr><th>$I[fontface]</th><td><table>";
		echo "<tr><td>&nbsp;</td><td><select name=\"font\" size=\"1\"><option value=\"\">* $I[roomdefault] *</option>";
		$F=load_fonts();
		foreach($F as $name=>$font){
			echo "<option style=\"$font\" ";
			if(strpos($U['style'], $font)!==false){
				echo 'selected ';
			}
			echo "value=\"$name\">$name</option>";
		}
		echo '</select></td><td>&nbsp;</td><td><label><input type="checkbox" name="bold" id="bold" value="on"';
		if(strpos($U['style'], 'font-weight:bold;')!==false){
			echo ' checked';
		}
		echo "><b>$I[bold]</b></label></td><td>&nbsp;</td><td><label><input type=\"checkbox\" name=\"italic\" id=\"italic\" value=\"on\"";
		if(strpos($U['style'], 'font-style:italic;')!==false){
			echo ' checked';
		}
		echo "><i>$I[italic]</i></label></td><td>&nbsp;</td><td><label><input type=\"checkbox\" name=\"small\" id=\"small\" value=\"on\"";
		if(strpos($U['style'], 'font-size:smaller;')!==false){
			echo ' checked';
		}
		echo "><small>$I[small]</small></label></td></tr></table></td></tr></table></td></tr>";
		thr();
	}
	echo '<tr><td>'.style_this(htmlspecialchars($U['nickname'])." : $I[fontexample]", $U['style']).'</td></tr>';
	thr();
	$bool_settings=['timestamps', 'nocache', 'sortupdown', 'hidechatters'];
	if(get_setting('imgembed')){
		$bool_settings[]='embed';
	}
	if($U['status']>=5 && get_setting('incognito')){
		$bool_settings[]='incognito';
	}
	foreach($bool_settings as $setting){
		echo "<tr><td><table id=\"$setting\"><tr><th>".$I[$setting].'</th><td>';
		echo "<label><input type=\"checkbox\" name=\"$setting\" value=\"on\"";
		if($U[$setting]){
			echo ' checked';
		}
		echo "><b>$I[enabled]</b></label></td></tr></table></td></tr>";
		thr();
	}
	if($U['status']>=2 && get_setting('eninbox')){
		echo "<tr><td><table id=\"eninbox\"><tr><th>$I[eninbox]</th><td>";
		echo "<select name=\"eninbox\" id=\"eninbox\">";
		echo '<option value="0"';
		if($U['eninbox']==0){
			echo ' selected';
		}
		echo ">$I[disabled]</option>";
		echo '<option value="1"';
		if($U['eninbox']==1){
			echo ' selected';
		}
		echo ">$I[eninall]</option>";
		echo '<option value="3"';
		if($U['eninbox']==3){
			echo ' selected';
		}
		echo ">$I[eninmem]</option>";
		echo '<option value="5"';
		if($U['eninbox']==5){
			echo ' selected';
		}
		echo ">$I[eninstaff]</option>";
		echo '</select></td></tr></table></td></tr>';
		thr();
	}
	echo "<tr><td><table id=\"tz\"><tr><th>$I[tz]</th><td>";
	echo "<select name=\"tz\">";
	$tzs=timezone_identifiers_list();
	foreach($tzs as $tz){
		echo "<option value=\"$tz\"";
		if($U['tz']==$tz){
			echo ' selected';
		}
		echo ">$tz</option>";
	}
	echo '</select></td></tr></table></td></tr>';
	thr();
	if($U['status']>=2){
		echo "<tr><td><table id=\"changepass\"><tr><th>$I[changepass]</th></tr>";
		echo '<tr><td><table>';
		echo "<tr><td>&nbsp;</td><td>$I[oldpass]</td><td><input type=\"password\" name=\"oldpass\" size=\"20\"></td></tr>";
		echo "<tr><td>&nbsp;</td><td>$I[newpass]</td><td><input type=\"password\" name=\"newpass\" size=\"20\"></td></tr>";
		echo "<tr><td>&nbsp;</td><td>$I[confirmpass]</td><td><input type=\"password\" name=\"confirmpass\" size=\"20\"></td></tr>";
		echo '</table></td></tr></table></td></tr>';
		thr();
		echo "<tr><td><table id=\"changenick\"><tr><th>$I[changenick]</th><td><table>";
		echo "<tr><td>&nbsp;</td><td>$I[newnickname]</td><td><input type=\"text\" name=\"newnickname\" size=\"20\">";
		echo '</table></td></tr></table></td></tr>';
		thr();
	}
	echo '<tr><td>'.submit($I['savechanges']).'</td></tr></table></form>';
	if($U['status']>1 && $U['status']<8){
		echo '<br>'.form('profile', 'delete').submit($I['deleteacc'], 'class="delbutton"').'</form>';
	}
	echo "<br><p id=\"changelang\">$I[changelang]";
	foreach($L as $lang=>$name){
		echo " <a href=\"$_SERVER[SCRIPT_NAME]?lang=$lang&amp;session=$U[session]&amp;action=controls\" target=\"controls\">$name</a>";
	}
	echo '</p><br>'.form('view').submit($I['backtochat'], 'class="backbutton"').'</form>';
	print_end();
}

