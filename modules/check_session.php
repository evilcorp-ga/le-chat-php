<?php
function check_session(){
	global $U;
	parse_sessions();
	check_expired();
	check_kicked();
	if($U['entry']==0){
		send_waiting_room();
	}
}

