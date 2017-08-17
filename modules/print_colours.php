<?php
function print_colours(){
	global $I;
	// Prints a short list with selected named HTML colours and filters out illegible text colours for the given background.
	// It's a simple comparison of weighted grey values. This is not very accurate but gets the job done well enough.
	// name=>[colour, greyval(colour)]
	$colours=['Beige'=>['F5F5DC', 242.25], 'Black'=>['000000', 0], 'Blue'=>['0000FF', 28.05], 'BlueViolet'=>['8A2BE2', 91.63], 'Brown'=>['A52A2A', 78.9], 'Cyan'=>['00FFFF', 178.5], 'DarkBlue'=>['00008B', 15.29], 'DarkGreen'=>['006400', 59], 'DarkRed'=>['8B0000', 41.7], 'DarkViolet'=>['9400D3', 67.61], 'DeepSkyBlue'=>['00BFFF', 140.74], 'Gold'=>['FFD700', 203.35], 'Grey'=>['808080', 128], 'Green'=>['008000', 75.52], 'HotPink'=>['FF69B4', 158.25], 'Indigo'=>['4B0082', 36.8], 'LightBlue'=>['ADD8E6', 204.64], 'LightGreen'=>['90EE90', 199.46], 'LimeGreen'=>['32CD32', 141.45], 'Magenta'=>['FF00FF', 104.55], 'Olive'=>['808000', 113.92], 'Orange'=>['FFA500', 173.85], 'OrangeRed'=>['FF4500', 117.21], 'Purple'=>['800080', 52.48], 'Red'=>['FF0000', 76.5], 'RoyalBlue'=>['4169E1', 106.2], 'SeaGreen'=>['2E8B57', 105.38], 'Sienna'=>['A0522D', 101.33], 'Silver'=>['C0C0C0', 192], 'Tan'=>['D2B48C', 184.6], 'Teal'=>['008080', 89.6], 'Violet'=>['EE82EE', 174.28], 'White'=>['FFFFFF', 255], 'Yellow'=>['FFFF00', 226.95], 'YellowGreen'=>['9ACD32', 172.65]];
	$greybg=greyval(get_setting('colbg'));
	foreach($colours as $name=>$colour){
		if(abs($greybg-$colour[1])>75){
			echo "<option value=\"$colour[0]\" style=\"color:#$colour[0];\">$I[$name]</option>";
		}
	}
}

