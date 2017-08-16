<?php
function get_linkfilters(){
	global $db, $memcached;
	if(MEMCACHED){
		$filters=$memcached->get(DBNAME . '-' . PREFIX . 'linkfilter');
	}
	if(!MEMCACHED || $memcached->getResultCode()!==Memcached::RES_SUCCESS){
		$filters=[];
		$result=$db->query('SELECT id, filtermatch, filterreplace, regex FROM ' . PREFIX . 'linkfilter;');
		while($filter=$result->fetch(PDO::FETCH_ASSOC)){
			$filters[]=['id'=>$filter['id'], 'match'=>$filter['filtermatch'], 'replace'=>$filter['filterreplace'], 'regex'=>$filter['regex']];
		}
		if(MEMCACHED){
			$memcached->set(DBNAME . '-' . PREFIX . 'linkfilter', $filters);
		}
	}
	return $filters;
}
