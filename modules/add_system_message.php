<?php
function add_system_message($mes){
	if($mes===''){
		return;
	}
	$sysmessage=[
		'postdate'	=>time(),
		'poststatus'	=>1,
		'poster'	=>'',
		'recipient'	=>'',
		'text'		=>"<span class=\"sysmsg\">$mes</span>",
		'delstatus'	=>4
	];
	write_message($sysmessage);
}

