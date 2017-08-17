<?php
function send_waiting_room(){
	global $I, $U, $db, $language;
	$ga=(int) get_setting('guestaccess');
	if($ga===3 && (get_count_mods()>0 || !get_setting('modfallback'))){
		$wait=false;
	}else{
		$wait=true;
	}
	check_expired();
	check_kicked();
	$timeleft=get_setting('entrywait')-(time()-$U['lastpost']);
	if($wait && ($timeleft<=0 || $ga===1)){
		$U['entry']=$U['lastpost'];
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET entry=lastpost WHERE session=?;');
		$stmt->execute([$U['session']]);
		send_frameset();
	}elseif(!$wait && $U['entry']!=0){
		send_frameset();
	}else{
		$refresh=(int) get_setting('defaultrefresh');
		print_start('waitingroom', $refresh, "$_SERVER[SCRIPT_NAME]?action=wait&session=$U[session]&lang=$language&nc=".substr(time(),-6));
		echo "<h2>$I[waitingroom]</h2><p>";
		if($wait){
			printf($I['waittext'], style_this(htmlspecialchars($U['nickname']), $U['style']), $timeleft);
		}else{
			printf($I['admwaittext'], style_this(htmlspecialchars($U['nickname']), $U['style']));
		}
		echo '</p><br><p>';
		printf($I['waitreload'], $refresh);
		echo '</p><br><br>';
		echo '<hr>'.form('wait');
		if(!isset($_REQUEST['session'])){
			echo hidden('session', $U['session']);
		}
		echo submit($I['reload']).'</form><br>';
		echo form('logout');
		if(!isset($_REQUEST['session'])){
			echo hidden('session', $U['session']);
		}
		echo submit($I['exit'], 'id="exitbutton"').'</form>';
		$rulestxt=get_setting('rulestxt');
		if(!empty($rulestxt)){
			echo "<div id=\"rules\"><h2>$I[rules]</h2><b>$rulestxt</b></div>";
		}
		print_end();
	}
}
