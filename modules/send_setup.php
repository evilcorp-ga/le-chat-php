<?php
function send_setup($C){
	global $I, $U;
	print_start('setup');
	echo "<h2>$I[setup]</h2>".form('setup', 'save');
	if(!isset($_REQUEST['session'])){
		echo hidden('session', $U['session']);
	}
	echo '<table id="guestaccess">';
	thr();
	$ga=(int) get_setting('guestaccess');
	echo "<tr><td><table><tr><th>$I[guestacc]</th><td>";
	echo '<select name="guestaccess">';
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
	echo '<option value="4"';
	if($ga===4){
		echo ' selected';
	}
	echo ">$I[disablechat]</option>";
	echo '</select></td></tr></table></td></tr>';
	thr();
	$englobal=(int) get_setting('englobalpass');
	echo "<tr><td><table id=\"globalpass\"><tr><th>$I[globalloginpass]</th><td>";
	echo '<table>';
	echo '<tr><td><select name="englobalpass">';
	echo '<option value="0"';
	if($englobal===0){
		echo ' selected';
	}
	echo ">$I[disabled]</option>";
	echo '<option value="1"';
	if($englobal===1){
		echo ' selected';
	}
	echo ">$I[enabled]</option>";
	echo '<option value="2"';
	if($englobal===2){
		echo ' selected';
	}
	echo ">$I[onlyguests]</option>";
	echo '</select></td><td>&nbsp;</td>';
	echo '<td><input type="text" name="globalpass" value="'.htmlspecialchars(get_setting('globalpass')).'"></td></tr>';
	echo '</table></td></tr></table></td></tr>';
	thr();
	$ga=(int) get_setting('guestreg');
	echo "<tr><td><table id=\"guestreg\"><tr><th>$I[guestreg]</th><td>";
	echo '<select name="guestreg">';
	echo '<option value="0"';
	if($ga===0){
		echo ' selected';
	}
	echo ">$I[disabled]</option>";
	echo '<option value="1"';
	if($ga===1){
		echo ' selected';
	}
	echo ">$I[assuguest]</option>";
	echo '<option value="2"';
	if($ga===2){
		echo ' selected';
	}
	echo ">$I[asmember]</option>";
	echo '</select></td></tr></table></td></tr>';
	thr();
	echo "<tr><td><table id=\"sysmessages\"><tr><th>$I[sysmessages]</th><td>";
	echo '<table>';
	foreach($C['msg_settings'] as $setting){
		echo "<tr><td>&nbsp;$I[$setting]</td><td>&nbsp;<input type=\"text\" name=\"$setting\" value=\"".get_setting($setting).'"></td></tr>';
	}
	echo '</table></td></tr></table></td></tr>';
	foreach($C['text_settings'] as $setting){
		thr();
		echo "<tr><td><table id=\"$setting\"><tr><th>".$I[$setting].'</th><td>';
		echo "<input type=\"text\" name=\"$setting\" value=\"".htmlspecialchars(get_setting($setting)).'">';
		echo '</td></tr></table></td></tr>';
	}
	foreach($C['colour_settings'] as $setting){
		thr();
		echo "<tr><td><table id=\"$setting\"><tr><th>".$I[$setting].'</th><td>';
		echo "<input type=\"color\" name=\"$setting\" value=\"#".htmlspecialchars(get_setting($setting)).'">';
		echo '</td></tr></table></td></tr>';
	}
	thr();
	echo "<tr><td><table id=\"captcha\"><tr><th>$I[captcha]</th><td>";
	echo '<table>';
	if(!extension_loaded('gd')){
		echo "<tr><td>$I[gdextrequired]</td></tr>";
	}else{
		echo '<tr><td><select name="dismemcaptcha">';
		$dismemcaptcha=(bool) get_setting('dismemcaptcha');
		echo '<option value="0"';
		if(!$dismemcaptcha){
			echo ' selected';
		}
		echo ">$I[enabled]</option>";
		echo '<option value="1"';
		if($dismemcaptcha){
			echo ' selected';
		}
		echo ">$I[onlyguests]</option>";
		echo '</select></td><td><select name="captcha">';
		$captcha=(int) get_setting('captcha');
		echo '<option value="0"';
		if($captcha===0){
			echo ' selected';
		}
		echo ">$I[disabled]</option>";
		echo '<option value="1"';
		if($captcha===1){
			echo ' selected';
		}
		echo ">$I[simple]</option>";
		echo '<option value="2"';
		if($captcha===2){
			echo ' selected';
		}
		echo ">$I[moderate]</option>";
		echo '<option value="3"';
		if($captcha===3){
			echo ' selected';
		}
		echo ">$I[extreme]</option>";
		echo '</select></td></tr>';
	}
	echo '</table></td></tr></table></td></tr>';
	thr();
	echo "<tr><td><table id=\"defaulttz\"><tr><th>$I[defaulttz]</th><td>";
	echo "<select name=\"defaulttz\">";
	$tzs=timezone_identifiers_list();
	$defaulttz=get_setting('defaulttz');
	foreach($tzs as $tz){
		echo "<option value=\"$tz\"";
		if($defaulttz==$tz){
			echo ' selected';
		}
		echo ">$tz</option>";
	}
	echo '</select>';
	echo '</td></tr></table></td></tr>';
	foreach($C['textarea_settings'] as $setting){
		thr();
		echo "<tr><td><table id=\"$setting\"><tr><th>".$I[$setting].'</th><td>';
		echo "<textarea name=\"$setting\" rows=\"4\" cols=\"60\">".htmlspecialchars(get_setting($setting)).'</textarea>';
		echo '</td></tr></table></td></tr>';
	}
	foreach($C['number_settings'] as $setting){
		thr();
		echo "<tr><td><table id=\"$setting\"><tr><th>".$I[$setting].'</th><td>';
		echo "<input type=\"number\" name=\"$setting\" value=\"".htmlspecialchars(get_setting($setting)).'">';
		echo '</td></tr></table></td></tr>';
	}
	foreach($C['bool_settings'] as $setting){
		thr();
		echo "<tr><td><table id=\"$setting\"><tr><th>".$I[$setting].'</th><td>';
		echo "<select name=\"$setting\">";
		$value=(bool) get_setting($setting);
		echo '<option value="0"';
		if(!$value){
			echo ' selected';
		}
		echo ">$I[disabled]</option>";
		echo '<option value="1"';
		if($value){
			echo ' selected';
		}
		echo ">$I[enabled]</option>";
		echo '</select></td></tr>';
		echo '</table></td></tr>';
	}
	thr();
	echo '<tr><td>'.submit($I['apply']).'</td></tr></table></form><br>';
	if($U['status']==8){
		echo '<table id="actions"><tr><td>';
		echo form('setup', 'backup');
		if(!isset($_REQUEST['session'])){
			echo hidden('session', $U['session']);
		}
		echo submit($I['backuprestore']).'</form></td><td>';
		echo form('setup', 'destroy');
		if(!isset($_REQUEST['session'])){
			echo hidden('session', $U['session']);
		}
		echo submit($I['destroy'], 'class="delbutton"').'</form></td></tr></table><br>';
	}
	echo form_target('_parent', 'logout');
	if(!isset($_REQUEST['session'])){
		echo hidden('session', $U['session']);
	}
	echo submit($I['logout'], 'id="exitbutton"').'</form>'.credit();
	print_end();
}
