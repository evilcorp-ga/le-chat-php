<?php
function get_timeout($lastpost, $expire){
	$s=($lastpost+60*$expire)-time();
	$m=floor($s/60);
	$s%=60;
	if($s<10){
		$s="0$s";
	}
	if($m>60){
		$h=floor($m/60);
		$m%=60;
		if($m<10){
			$m="0$m";
		}
		echo "$h:$m:$s";
	}else{
		echo "$m:$s";
	}
}

