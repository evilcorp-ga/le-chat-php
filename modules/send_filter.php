<?php
function send_filter($arg=''){
	global $I, $U;
	print_start('filter');
	echo "<h2>$I[filter]</h2><i>$arg</i><table>";
	thr();
	echo '<tr><th><table style="width:100%;"><tr>';
	echo "<td style=\"width:8em;\">$I[fid]</td>";
	echo "<td style=\"width:12em;\">$I[match]</td>";
	echo "<td style=\"width:12em;\">$I[replace]</td>";
	echo "<td style=\"width:9em;\">$I[allowpm]</td>";
	echo "<td style=\"width:5em;\">$I[regex]</td>";
	echo "<td style=\"width:5em;\">$I[kick]</td>";
	echo "<td style=\"width:5em;\">$I[cs]</td>";
	echo "<td style=\"width:5em;\">$I[apply]</td>";
	echo '</tr></table></th></tr>';
	$filters=get_filters();
	foreach($filters as $filter){
		if($filter['allowinpm']==1){
			$check=' checked';
		}else{
			$check='';
		}
		if($filter['regex']==1){
			$checked=' checked';
		}else{
			$checked='';
			$filter['match']=preg_replace('/(\\\\(.))/u', "$2", $filter['match']);
		}
		if($filter['kick']==1){
			$checkedk=' checked';
		}else{
			$checkedk='';
		}
		if($filter['cs']==1){
			$checkedcs=' checked';
		}else{
			$checkedcs='';
		}
		echo '<tr><td>';
		echo form('admin', 'filter').hidden('id', $filter['id']);
		echo "<table style=\"width:100%;\"><tr><th style=\"width:8em;\">$I[filter] $filter[id]:</th>";
		echo "<td style=\"width:12em;\"><input type=\"text\" name=\"match\" value=\"$filter[match]\" size=\"20\" style=\"$U[style]\"></td>";
		echo '<td style="width:12em;"><input type="text" name="replace" value="'.htmlspecialchars($filter['replace'])."\" size=\"20\" style=\"$U[style]\"></td>";
		echo "<td style=\"width:9em;\"><label><input type=\"checkbox\" name=\"allowinpm\" value=\"1\"$check>$I[allowpm]</label></td>";
		echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"regex\" value=\"1\"$checked>$I[regex]</label></td>";
		echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"kick\" value=\"1\"$checkedk>$I[kick]</label></td>";
		echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"cs\" value=\"1\"$checkedcs>$I[cs]</label></td>";
		echo '<td class="filtersubmit" style="width:5em;">'.submit($I['change']).'</td></tr></table></form></td></tr>';
	}
	echo '<tr><td>';
	echo form('admin', 'filter').hidden('id', '+');
	echo "<table style=\"width:100%;\"><tr><th style=\"width:8em\">$I[newfilter]</th>";
	echo "<td style=\"width:12em;\"><input type=\"text\" name=\"match\" value=\"\" size=\"20\" style=\"$U[style]\"></td>";
	echo "<td style=\"width:12em;\"><input type=\"text\" name=\"replace\" value=\"\" size=\"20\" style=\"$U[style]\"></td>";
	echo "<td style=\"width:9em;\"><label><input type=\"checkbox\" name=\"allowinpm\" id=\"allowinpm\" value=\"1\">$I[allowpm]</label></td>";
	echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"regex\" id=\"regex\" value=\"1\">$I[regex]</label></td>";
	echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"kick\" id=\"kick\" value=\"1\">$I[kick]</label></td>";
	echo "<td style=\"width:5em;\"><label><input type=\"checkbox\" name=\"cs\" id=\"cs\" value=\"1\">$I[cs]</label></td>";
	echo '<td class="filtersubmit" style="width:5em;">'.submit($I['add']).'</td></tr></table></form></td></tr>';
	echo "</table><br>";
	echo form('admin', 'filter').submit($I['reload']).'</form>';
	print_end();
}
