<?php
function send_backup($C){
	global $I, $db;
	$code=[];
	if($_REQUEST['do']==='backup'){
		if(isset($_REQUEST['settings'])){
			foreach($C['settings'] as $setting){
				$code['settings'][$setting]=get_setting($setting);
			}
		}
		if(isset($_REQUEST['filter'])){
			$result=$db->query('SELECT * FROM ' . PREFIX . 'filter;');
			while($filter=$result->fetch(PDO::FETCH_ASSOC)){
				$code['filters'][]=['match'=>$filter['filtermatch'], 'replace'=>$filter['filterreplace'], 'allowinpm'=>$filter['allowinpm'], 'regex'=>$filter['regex'], 'kick'=>$filter['kick'], 'cs'=>$filter['cs']];
			}
			$result=$db->query('SELECT * FROM ' . PREFIX . 'linkfilter;');
			while($filter=$result->fetch(PDO::FETCH_ASSOC)){
				$code['linkfilters'][]=['match'=>$filter['filtermatch'], 'replace'=>$filter['filterreplace'], 'regex'=>$filter['regex']];
			}
		}
		if(isset($_REQUEST['members'])){
			$result=$db->query('SELECT * FROM ' . PREFIX . 'members;');
			while($member=$result->fetch(PDO::FETCH_ASSOC)){
				$code['members'][]=$member;
			}
		}
		if(isset($_REQUEST['notes'])){
			$result=$db->query('SELECT * FROM ' . PREFIX . "notes;");
			while($note=$result->fetch(PDO::FETCH_ASSOC)){
				if(MSGENCRYPTED){
					$note['text']=openssl_decrypt($note['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
				}
				$code['notes'][]=$note;
			}
		}
	}
	if(isset($_REQUEST['settings'])){
		$chksettings=' checked';
	}else{
		$chksettings='';
	}
	if(isset($_REQUEST['filter'])){
		$chkfilters=' checked';
	}else{
		$chkfilters='';
	}
	if(isset($_REQUEST['members'])){
		$chkmembers=' checked';
	}else{
		$chkmembers='';
	}
	if(isset($_REQUEST['notes'])){
		$chknotes=' checked';
	}else{
		$chknotes='';
	}
	print_start('backup');
	echo "<h2>$I[backuprestore]</h2><table>";
	thr();
	if(!extension_loaded('json')){
		echo "<tr><td>$I[jsonextrequired]</td></tr>";
	}else{
		echo '<tr><td>'.form('setup', 'backup');
		echo '<table id="backup"><tr><td id="backupcheck">';
		echo "<label><input type=\"checkbox\" name=\"settings\" id=\"backupsettings\" value=\"1\"$chksettings>$I[settings]</label>";
		echo "<label><input type=\"checkbox\" name=\"filter\" id=\"backupfilter\" value=\"1\"$chkfilters>$I[filter]</label>";
		echo "<label><input type=\"checkbox\" name=\"members\" id=\"backupmembers\" value=\"1\"$chkmembers>$I[members]</label>";
		echo "<label><input type=\"checkbox\" name=\"notes\" id=\"backupnotes\" value=\"1\"$chknotes>$I[notes]</label>";
		echo '</td><td id="backupsubmit">'.submit($I['backup']).'</td></tr></table></form></td></tr>';
		thr();
		echo '<tr><td>'.form('setup', 'restore');
		echo '<table id="restore">';
		echo "<tr><td colspan=\"2\"><textarea name=\"restore\" rows=\"4\" cols=\"60\">".htmlspecialchars(json_encode($code)).'</textarea></td></tr>';
		echo "<tr><td id=\"restorecheck\"><label><input type=\"checkbox\" name=\"settings\" id=\"restoresettings\" value=\"1\"$chksettings>$I[settings]</label>";
		echo "<label><input type=\"checkbox\" name=\"filter\" id=\"restorefilter\" value=\"1\"$chkfilters>$I[filter]</label>";
		echo "<label><input type=\"checkbox\" name=\"members\" id=\"restoremembers\" value=\"1\"$chkmembers>$I[members]</label>";
		echo "<label><input type=\"checkbox\" name=\"notes\" id=\"restorenotes\" value=\"1\"$chknotes>$I[notes]</label>";
		echo '</td><td id="restoresubmit">'.submit($I['restore']).'</td></tr></table>';
		echo '</form></td></tr>';
	}
	thr();
	echo '<tr><td>'.form('setup').submit($I['initgosetup'], 'class="backbutton"')."</form></tr></td>";
	echo '</table>';
	print_end();
}
