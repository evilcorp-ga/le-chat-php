<?php
/*
* LE CHAT-PHP - a PHP Chat based on LE CHAT - Main program
*
* Copyright (C) 2015-2017 Daniel Winzen <d@winzen4.de>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
* status codes
* 0 - Kicked/Banned
* 1 - Guest
* 2 - Applicant
* 3 - Member
* 4 - System message
* 5 - Moderator
* 6 - Super-Moderator
* 7 - Admin
* 8 - Super-Admin
* 9 - Private messages
*/

include "./modules/add_message.php";
include "./modules/add_system_message.php";
include "./modules/add_user_defaults.php";
include "./modules/amend.profile.php";
include "./modules/apply_filter.php";
include "./modules/apply_linkfilter.php";
include "./modules/apply_mention.php";
include "./modules/approve_session.php";
include "./modules/change_status.php";
include "./modules/check_db.php";
include "./modules/check_expired.php";
include "./modules/check_filter_match.php";
include "./modules/check_init.php";
include "./modules/check_kicked.php";
include "./modules/check_login.php";
include "./modules/check_member.php";
include "./modules/check_session.php";
include "./modules/clean_inbox_selected.php";
include "./modules/clean_room.php";
include "./modules/clean_selected.php";
include "./modules/create_hotlinks.php";
include "./modules/create_session.php";
include "./modules/credit.php";
include "./modules/cron.php";
include "./modules/del_all_messages.php";
include "./modules/del_last_message.php";
include "./modules/delete_account.php";
include "./modules/destroy_chat.php";
include "./modules/form.php";
include "./modules/form_target.php";
include "./modules/get_count_mods.php";
include "./modules/get_filters.php";
include "./modules/get_linkfilters.php";
include "./modules/get_nowchatting.php";
include "./modules/get_setting.php";
include "./modules/get_timeout.php";
include "./modules/greyval.php";
include "./modules/hidden.php";
include "./modules/init_chat.php";
include "./modules/kick_chatter.php";
include "./modules/kill_chatter.php";
include "./modules/kill_session.php";
include "./modules/load_config.php";
include "./modules/load_fonts.php";
include "./modules/load_lang.php";
include "./modules/logout_chatter.php";
include "./modules/manage_filter.php";
include "./modules/manage_linkfilter.php";
include "./modules/meta_html.php";
include "./modules/parse_sessions.php";
include "./modules/passreset.php";
include "./modules/prepare_message_print.php";
include "./modules/print_chatters.php";
include "./modules/print_colours.php";
include "./modules/print_end.php";
include "./modules/print_messages.php";
include "./modules/print_notifications.php";
include "./modules/print_start.php";
include "./modules/print_stylesheet.php";
include "./modules/register_guest.php";
include "./modules/register_new.php";
include "./modules/restore_backup.php";
include "./modules/route.php";
include "./modules/route_admin.php";
include "./modules/route_setup.php";
include "./modules/save_profile.php";
include "./modules/save_setup.php";
include "./modules/send_access_denied.php";
include "./modules/send_admin.php";
include "./modules/send_alogin.php";
include "./modules/send_approve_waiting.php";
include "./modules/send_backup.php";
include "./modules/send_captcha.php";
include "./modules/send_chat_disabled.php";
include "./modules/send_choose_messages.php";
include "./modules/send_colours.php";
include "./modules/send_controls.php";
include "./modules/send_del_confirm.php";
include "./modules/send_delete_account.php";
include "./modules/send_destroy_chat.php";
include "./modules/send_download.php";
include "./modules/send_error.php";
include "./modules/send_fatal_error.php";
include "./modules/send_filter.php";
include "./modules/send_frameset.php";
include "./modules/send_greeting.php";
include "./modules/send_headers.php";
include "./modules/send_help.php";
include "./modules/send_inbox.php";
include "./modules/send_init.php";
include "./modules/send_linkfilter.php";
include "./modules/send_login.php";
include "./modules/send_logout.php";
include "./modules/send_messages.php";
include "./modules/send_notes.php";
include "./modules/send_post.php";
include "./modules/send_profile.php";
include "./modules/send_redirect.php";
include "./modules/send_sessions.php";
include "./modules/send_setup.php";
include "./modules/send_update.php";
include "./modules/send_waiting_room.php";
include "./modules/set_default_tz.php";
include "./modules/set_new_nickname.php";
include "./modules/style_this.php";
include "./modules/submit.php";
include "./modules/thr.php";
include "./modules/update_db.php";
include "./modules/update_setting.php";
include "./modules/valid_admin.php";
include "./modules/valid_nick.php";
include "./modules/valid_pass.php";
include "./modules/valid_regex.php";
include "./modules/validate_input.php";
include "./modules/write_message.php";
include "./modules/write_new_session.php";
send_headers();
// initialize and load variables/configuration
$I=[];// Translations
$L=[];// Languages
$U=[];// This user data
$db;// Database connection
$memcached;// Memcached connection
$language;// user selected language
load_config();
// set session variable to cookie if cookies are enabled
if(!isset($_REQUEST['session']) && isset($_COOKIE[COOKIENAME])){
	$_REQUEST['session']=$_COOKIE[COOKIENAME];
}
load_lang();
check_db();
cron();
route();

