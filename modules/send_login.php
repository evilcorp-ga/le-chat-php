<?php
function send_login(){
	global $I, $L;
	$ga=(int) get_setting('guestaccess');
	if($ga===4){
		send_chat_disabled();
	}
	print_start('login');
	$englobal=(int) get_setting('englobalpass');
	echo '<h1 id="chatname">'.get_setting('chatname').'</h1>';
	echo form_target('_parent', 'login');
	if($englobal===1 && isset($_REQUEST['globalpass'])){
		echo hidden('globalpass', $_REQUEST['globalpass']);
	}
	echo '<table>';
	if($englobal!==1 || (isset($_REQUEST['globalpass']) && $_REQUEST['globalpass']==get_setting('globalpass'))){
		echo "<tr><td>$I[nick]</td><td><input type=\"text\" name=\"nick\" size=\"15\" autofocus></td></tr>";
		echo "<tr><td>$I[pass]</td><td><input type=\"password\" name=\"pass\" size=\"15\"></td></tr>";
		send_captcha();
		if($ga!==0){
			if(get_setting('guestreg')!=0){
				echo "<tr><td>$I[regpass]</td><td><input type=\"password\" name=\"regpass\" size=\"15\" placeholder=\"$I[optional]\"></td></tr>";
			}
			if($englobal===2){
				echo "<tr><td>$I[globalloginpass]</td><td><input type=\"password\" name=\"globalpass\" size=\"15\"></td></tr>";
			}
			echo "<tr><td colspan=\"2\">$I[choosecol]<br><select name=\"colour\"><option value=\"\">* $I[randomcol] *</option>";
			print_colours();
			echo '</select></td></tr>';
		}else{
			echo "<tr><td colspan=\"2\">$I[noguests]</td></tr>";
		}
		echo '<tr><td colspan="2">'.submit($I['enter']).'</td></tr></table></form>';
		get_nowchatting();
		echo '<br><div id="topic">';
		echo get_setting('topic');
		echo '</div>';
		$rulestxt=get_setting('rulestxt');
		if(!empty($rulestxt)){
			echo "<div id=\"rules\"><h2>$I[rules]</h2><b>$rulestxt</b></div>";
		}
	}else{
		echo "<tr><td>$I[globalloginpass]</td><td><input type=\"password\" name=\"globalpass\" size=\"15\" autofocus></td></tr>";
		if($ga===0){
			echo "<tr><td colspan=\"2\">$I[noguests]</td></tr>";
		}
		echo '<tr><td colspan="2">'.submit($I['enter']).'</td></tr></table></form>';
	}
	echo "<p id=\"changelang\">$I[changelang]";
	foreach($L as $lang=>$name){
		echo " <a href=\"$_SERVER[SCRIPT_NAME]?lang=$lang\">$name</a>";
	}
	echo '</p>'.credit();
	print_end();
}

