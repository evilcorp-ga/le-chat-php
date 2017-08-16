<?php
function send_linkfilter($arg=''){
	global $I, $U;
	print_start('linkfilter');
	echo "<h2>$I[linkfilter]</h2><i>$arg</i><table>";
	thr();
	echo '<tr><th><table style="width:100%;"><tr>';
	echo "<td style=\"width:8em;\">$I[fid]</td>";
	echo "<td style=\"width:12em;\">$I[match]</td>";
	echo "<td style=\"width:12em;\">$I[replace]</td>";
	echo "<td style=\"width:5em;\">$I[regex]</td>";
	echo "<td style=\"width:5em;\">$I[apply]</td>";
	echo '</tr></table></th></tr>';
	$filters=get_linkfilters();
	foreach($filters as $filter){
		if($filter['regex']==1){
			$checked=' checked';
		}else{
			$checked='';
			$filter['match']=preg_replace('/(\\\\(.))/u', "$2", $filter['match']);
		}
		echo '<tr><td>';
		echo form('admin', 'linkfilter').hidden('id', $filter['id']);
		echo "<table style=\"width:100%;\"><tr><th style=\"width:8em;\">$I[filter] $filter[id]:</th>";
		echo "<td style=\"width:12em;\"><input type=\"text\" name=\"match\" value=\"$filter[match]\" size=\"20\" style=\"$U[style]\"></td>";
		echo '<td style="width:12em;"><input type="text" name="replace" value="'.htmlspecialchars($filter['replace'])."\" size=\"20\" style=\"$U[style]\"></td>";
		echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"regex\" value=\"1\"$checked>$I[regex]</label></td>";
		echo '<td class="filtersubmit" style="width:5em;">'.submit($I['change']).'</td></tr></table></form></td></tr>';
	}
	echo '<tr><td>';
	echo form('admin', 'linkfilter').hidden('id', '+');
	echo "<table style=\"width:100%;\"><tr><th style=\"width:8em;\">$I[newfilter]</th>";
	echo "<td style=\"width:12em;\"><input type=\"text\" name=\"match\" value=\"\" size=\"20\" style=\"$U[style]\"></td>";
	echo "<td style=\"width:12em;\"><input type=\"text\" name=\"replace\" value=\"\" size=\"20\" style=\"$U[style]\"></td>";
	echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"regex\" value=\"1\">$I[regex]</label></td>";
	echo '<td class="filtersubmit" style="width:5em;">'.submit($I['add']).'</td></tr></table></form></td></tr>';
	echo "</table><br>";
	echo form('admin', 'linkfilter').submit($I['reload']).'</form>';
	print_end();
}
