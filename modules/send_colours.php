<?php
function send_colours(){
	global $I;
	print_start('colours');
	echo "<h2>$I[colourtable]</h2><kbd><b>";
	for($red=0x00;$red<=0xFF;$red+=0x33){
		for($green=0x00;$green<=0xFF;$green+=0x33){
			for($blue=0x00;$blue<=0xFF;$blue+=0x33){
				$hcol=sprintf('%02X%02X%02X', $red, $green, $blue);
				echo "<span style=\"color:#$hcol\">$hcol</span> ";
			}
			echo '<br>';
		}
		echo '<br>';
	}
	echo '</b></kbd>'.form('profile').submit($I['backtoprofile'], ' class="backbutton"').'</form>';
	print_end();
}

