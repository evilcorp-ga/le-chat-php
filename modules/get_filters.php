<?php
function get_filters(){
	global $db, $memcached;
	if(MEMCACHED){
		$filters=$memcached->get(DBNAME . '-' . PREFIX . 'filter');
	}
	if(!MEMCACHED || $memcached->getResultCode()!==Memcached::RES_SUCCESS){
		$filters=[];
		$result=$db->query('SELECT id, filtermatch, filterreplace, allowinpm, regex, kick, cs FROM ' . PREFIX . 'filter;');
		while($filter=$result->fetch(PDO::FETCH_ASSOC)){
			$filters[]=['id'=>$filter['id'], 'match'=>$filter['filtermatch'], 'replace'=>$filter['filterreplace'], 'allowinpm'=>$filter['allowinpm'], 'regex'=>$filter['regex'], 'kick'=>$filter['kick'], 'cs'=>$filter['cs']];
		}
		if(MEMCACHED){
			$memcached->set(DBNAME . '-' . PREFIX . 'filter', $filters);
		}
	}
	return $filters;
}
