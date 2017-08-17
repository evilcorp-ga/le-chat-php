<?php
function send_help(){
	global $I, $U;
	print_start('help');
	$rulestxt=get_setting('rulestxt');
	if(!empty($rulestxt)){
		echo "<div id=\"rules\"><h2>$I[rules]</h2>$rulestxt<br></div><hr>";
	}
	echo "<h2>$I[help]</h2>$I[helpguest]";
	if(get_setting('imgembed')){
		echo "<br>$I[helpembed]";
	}
	if($U['status']>=3){
		echo "<br>$I[helpmem]<br>";
		if($U['status']>=5){
			echo "<br>$I[helpmod]<br>";
			if($U['status']>=7){
				echo "<br>$I[helpadm]<br>";
			}
		}
	}
	echo '<br><hr><div id="backcredit">'.form('view').submit($I['backtochat'], 'class="backbutton"').'</form>'.credit().'</div>';
	print_end();
}

