<?php
function send_notes($type){
	global $I, $U, $db;
	print_start('notes');
	$personalnotes=(bool) get_setting('personalnotes');
	if($U['status']>=5 && ($personalnotes || $U['status']>6)){
		echo '<table><tr>';
		if($U['status']>6){
			echo '<td>'.form_target('view', 'notes', 'admin').submit($I['admnotes']).'</form></td>';
		}
		echo '<td>'.form_target('view', 'notes', 'staff').submit($I['staffnotes']).'</form></td>';
		if($personalnotes){
			echo '<td>'.form_target('view', 'notes').submit($I['personalnotes']).'</form></td>';
		}
		echo '</tr></table>';
	}
	if($type===1){
		echo "<h2>$I[staffnotes]</h2><p>";
		$hiddendo=hidden('do', 'staff');
	}elseif($type===0){
		echo "<h2>$I[adminnotes]</h2><p>";
		$hiddendo=hidden('do', 'admin');
	}else{
		echo "<h2>$I[personalnotes]</h2><p>";
		$hiddendo='';
	}
	if(isset($_REQUEST['text'])){
		if(MSGENCRYPTED){
			$_REQUEST['text']=openssl_encrypt($_REQUEST['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
		}
		$time=time();
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'notes (type, lastedited, editedby, text) VALUES (?, ?, ?, ?);');
		$stmt->execute([$type, $time, $U['nickname'], $_REQUEST['text']]);
		echo "<b>$I[notessaved]</b> ";
	}
	$dateformat=get_setting('dateformat');
	if($type!==2){
		$stmt=$db->prepare('SELECT COUNT(*) FROM ' . PREFIX . 'notes WHERE type=?;');
		$stmt->execute([$type]);
	}else{
		$stmt=$db->prepare('SELECT COUNT(*) FROM ' . PREFIX . 'notes WHERE type=? AND editedby=?;');
		$stmt->execute([$type, $U['nickname']]);
	}
	$num=$stmt->fetch(PDO::FETCH_NUM);
	if(!empty($_REQUEST['revision'])){
		$revision=intval($_REQUEST['revision']);
	}else{
		$revision=0;
	}
	if($type!==2){
		$stmt=$db->prepare('SELECT * FROM ' . PREFIX . "notes WHERE type=? ORDER BY id DESC LIMIT 1 OFFSET $revision;");
		$stmt->execute([$type]);
	}else{
		$stmt=$db->prepare('SELECT * FROM ' . PREFIX . "notes WHERE type=? AND editedby=? ORDER BY id DESC LIMIT 1 OFFSET $revision;");
		$stmt->execute([$type, $U['nickname']]);
	}
	if($note=$stmt->fetch(PDO::FETCH_ASSOC)){
		printf($I['lastedited'], htmlspecialchars($note['editedby']), date($dateformat, $note['lastedited']));
	}else{
		$note['text']='';
	}
	if(MSGENCRYPTED){
		$note['text']=openssl_decrypt($note['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
	}
	echo "</p>".form('notes');
	echo "$hiddendo<textarea name=\"text\">".htmlspecialchars($note['text']).'</textarea><br>';
	echo submit($I['savenotes']).'</form><br>';
	if($num[0]>1){
		echo "<br><table><tr><td>$I[revisions]</td>";
		if($revision<$num[0]-1){
			echo '<td>'.form('notes').hidden('revision', $revision+1);
			echo $hiddendo.submit($I['older']).'</form></td>';
		}
		if($revision>0){
			echo '<td>'.form('notes').hidden('revision', $revision-1);
			echo $hiddendo.submit($I['newer']).'</form></td>';
		}
		echo '</tr></table>';
	}
	print_end();
}
