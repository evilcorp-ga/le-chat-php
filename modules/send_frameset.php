<?php
function send_frameset(){
	global $I, $U, $db, $language;
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd"><html><head>'.meta_html();
	echo '<title>'.get_setting('chatname').'</title>';
	print_stylesheet();
	echo '</head>';
	if(isset($_REQUEST['sort'])){
		if($_REQUEST['sort']==1){
			$U['sortupdown']=1;
			$tmp=$U['nocache'];
			$U['nocache']=$U['nocache_old'];
			$U['nocache_old']=$tmp;
		}else{
			$U['sortupdown']=0;
			$tmp=$U['nocache'];
			$U['nocache']=$U['nocache_old'];
			$U['nocache_old']=$tmp;
		}
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'sessions SET sortupdown=?, nocache=?, nocache_old=? WHERE nickname=?;');
		$stmt->execute([$U['sortupdown'], $U['nocache'], $U['nocache_old'], $U['nickname']]);
		if($U['status']>1){
			$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET sortupdown=?, nocache=?, nocache_old=? WHERE nickname=?;');
			$stmt->execute([$U['sortupdown'], $U['nocache'], $U['nocache_old'], $U['nickname']]);
		}
	}
	if($U['sortupdown']){
		$bottom='#bottom';
	}else{
		$bottom='';
	}
	if(($U['status']>=5 || ($U['status']>2 && get_count_mods()==0)) && get_setting('enfileupload')){
		$postheight=120;
	}else{
		$postheight=100;
	}
	if((!isset($_REQUEST['sort']) && !$U['sortupdown']) || (isset($_REQUEST['sort']) && $_REQUEST['sort']==0)){
		echo "<frameset rows=\"$postheight,*,45\" border=\"3\" frameborder=\"3\" framespacing=\"3\">";
		echo "<frame name=\"post\" src=\"$_SERVER[SCRIPT_NAME]?action=post&session=$U[session]&lang=$language\">";
		if(get_setting('enablegreeting')){
			echo "<frame name=\"view\" src=\"$_SERVER[SCRIPT_NAME]?action=greeting&session=$U[session]&lang=$language\">";
		}else{
			echo "<frame name=\"view\" src=\"$_SERVER[SCRIPT_NAME]?action=view&session=$U[session]&lang=$language$bottom\">";
		}
		echo "<frame name=\"controls\" src=\"$_SERVER[SCRIPT_NAME]?action=controls&session=$U[session]&lang=$language&sort=1\">";
	}else{
		echo "<frameset rows=\"45,*,$postheight\" border=\"3\" frameborder=\"3\" framespacing=\"3\">";
		echo "<frame name=\"controls\" src=\"$_SERVER[SCRIPT_NAME]?action=controls&session=$U[session]&lang=$language&sort=0\">";
		if(get_setting('enablegreeting')){
			echo "<frame name=\"view\" src=\"$_SERVER[SCRIPT_NAME]?action=greeting&session=$U[session]&lang=$language\">";
		}else{
			echo "<frame name=\"view\" src=\"$_SERVER[SCRIPT_NAME]?action=view&session=$U[session]&lang=$language$bottom\">";
		}
		echo "<frame name=\"post\" src=\"$_SERVER[SCRIPT_NAME]?action=post&session=$U[session]&lang=$language\">";
	}
	echo "<noframes><body>$I[noframes]".form_target('_parent', 'login').submit($I['backtologin'], 'class="backbutton"').'</form></body></noframes></frameset></html>';
	exit;
}
