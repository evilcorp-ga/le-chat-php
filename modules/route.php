<?php
function route(){
	global $U;
	if(!isset($_REQUEST['action'])){
		send_login();
	}elseif($_REQUEST['action']==='view'){
		check_session();
		send_messages();
	}elseif($_REQUEST['action']==='redirect' && !empty($_REQUEST['url'])){
		send_redirect($_REQUEST['url']);
	}elseif($_REQUEST['action']==='wait'){
		parse_sessions();
		send_waiting_room();
	}elseif($_REQUEST['action']==='post'){
		check_session();
		if(isset($_REQUEST['kick']) && isset($_REQUEST['sendto']) && $_REQUEST['sendto']!=='s &'){
			if($U['status']>=5 || ($U['status']>=3 && get_count_mods()==0 && get_setting('memkick'))){
				if(isset($_REQUEST['what']) && $_REQUEST['what']==='purge'){
					kick_chatter([$_REQUEST['sendto']], $_REQUEST['message'], true);
				}else{
					kick_chatter([$_REQUEST['sendto']], $_REQUEST['message'], false);
				}
			}
		}elseif(isset($_REQUEST['message']) && isset($_REQUEST['sendto'])){
			send_post(validate_input());
		}
		send_post();
	}elseif($_REQUEST['action']==='login'){
		check_login();
		send_frameset();
	}elseif($_REQUEST['action']==='controls'){
		check_session();
		send_controls();
	}elseif($_REQUEST['action']==='greeting'){
		check_session();
		send_greeting();
	}elseif($_REQUEST['action']==='delete'){
		check_session();
		if($_REQUEST['what']==='all'){
			if(isset($_REQUEST['confirm'])){
				del_all_messages($U['nickname'], $U['status']==1 ? $U['entry'] : 0);
			}else{
				send_del_confirm();
			}
		}elseif($_REQUEST['what']==='last'){
			del_last_message();
		}
		send_post();
	}elseif($_REQUEST['action']==='profile'){
		check_session();
		$arg='';
		if(!isset($_REQUEST['do'])){
		}elseif($_REQUEST['do']==='save'){
			$arg=save_profile();
		}elseif($_REQUEST['do']==='delete'){
			if(isset($_REQUEST['confirm'])){
				delete_account();
			}else{
				send_delete_account();
			}
		}
		send_profile($arg);
	}elseif($_REQUEST['action']==='logout'){
		kill_session();
		send_logout();
	}elseif($_REQUEST['action']==='colours'){
		check_session();
		send_colours();
	}elseif($_REQUEST['action']==='notes'){
		check_session();
		if(isset($_REQUEST['do']) && $_REQUEST['do']==='admin' && $U['status']>6){
			send_notes(0);
		}elseif(isset($_REQUEST['do']) && $_REQUEST['do']==='staff' && $U['status']>=5){
			send_notes(1);
		}
		if($U['status']<3 || !get_setting('personalnotes')){
			send_access_denied();
		}
		send_notes(2);
	}elseif($_REQUEST['action']==='help'){
		check_session();
		send_help();
	}elseif($_REQUEST['action']==='inbox'){
		check_session();
		if(isset($_REQUEST['do'])){
			clean_inbox_selected();
		}
		send_inbox();
	}elseif($_REQUEST['action']==='download'){
		send_download();
	}elseif($_REQUEST['action']==='admin'){
		check_session();
		send_admin(route_admin());
	}elseif($_REQUEST['action']==='setup'){
		route_setup();
	}else{
		send_login();
	}
}