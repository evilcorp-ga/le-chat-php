<?php
function send_controls(){
	global $I, $U;
	print_start('controls');
	$personalnotes=(bool) get_setting('personalnotes');
	echo '<table><tr>';
	echo '<td>'.form_target('post', 'post').submit($I['reloadpb']).'</form></td>';
	echo '<td>'.form_target('view', 'view').submit($I['reloadmsgs']).'</form></td>';
	echo '<td>'.form_target('view', 'profile').submit($I['chgprofile']).'</form></td>';
	if($U['status']>=5){
		echo '<td>'.form_target('view', 'admin').submit($I['adminbtn']).'</form></td>';
		if(!$personalnotes){
			echo '<td>'.form_target('view', 'notes', 'staff').submit($I['notes']).'</form></td>';
		}
	}
	if($U['status']>=3){
		if($personalnotes){
			echo '<td>'.form_target('view', 'notes').submit($I['notes']).'</form></td>';
		}
		echo '<td>'.form_target('_blank', 'login').submit($I['clone']).'</form></td>';
	}
	if(!isset($_REQUEST['sort'])){
		$sort=0;
	}else{
		$sort=$_REQUEST['sort'];
	}
	echo '<td>'.form_target('_parent', 'login').hidden('sort', $sort).submit($I['sortframe']).'</form></td>';
	echo '<td>'.form_target('view', 'help').submit($I['randh']).'</form></td>';
	echo '<td>'.form_target('_parent', 'logout').submit($I['exit'], 'id="exitbutton"').'</form></td>';
	echo '</tr></table>';
	print_end();
}

