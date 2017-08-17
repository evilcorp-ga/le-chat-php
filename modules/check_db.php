<?php
function check_db(){
	global $I, $db, $memcached;
	$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::ATTR_PERSISTENT=>PERSISTENT];
	try{
		if(DBDRIVER===0){
			if(!extension_loaded('pdo_mysql')){
				send_fatal_error($I['pdo_mysqlextrequired']);
			}
			$db=new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8mb4', DBUSER, DBPASS, $options);
		}elseif(DBDRIVER===1){
			if(!extension_loaded('pdo_pgsql')){
				send_fatal_error($I['pdo_pgsqlextrequired']);
			}
			$db=new PDO('pgsql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS, $options);
		}else{
			if(!extension_loaded('pdo_sqlite')){
				send_fatal_error($I['pdo_sqliteextrequired']);
			}
			$db=new PDO('sqlite:' . SQLITEDBFILE, NULL, NULL, $options);
		}
	}catch(PDOException $e){
		try{
			//Attempt to create database
			if(DBDRIVER===0){
				$db=new PDO('mysql:host=' . DBHOST, DBUSER, DBPASS, $options);
				if(false!==$db->exec('CREATE DATABASE ' . DBNAME)){
					$db=new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8mb4', DBUSER, DBPASS, $options);
				}else{
					send_fatal_error($I['nodbsetup']);
				}

			}elseif(DBDRIVER===1){
				$db=new PDO('pgsql:host=' . DBHOST, DBUSER, DBPASS, $options);
				if(false!==$db->exec('CREATE DATABASE ' . DBNAME)){
					$db=new PDO('pgsql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS, $options);
				}else{
					send_fatal_error($I['nodbsetup']);
				}
			}else{
				if(isset($_REQUEST['action']) && $_REQUEST['action']==='setup'){
					send_fatal_error($I['nodbsetup']);
				}else{
					send_fatal_error($I['nodb']);
				}
			}
		}catch(PDOException $e){
			if(isset($_REQUEST['action']) && $_REQUEST['action']==='setup'){
				send_fatal_error($I['nodbsetup']);
			}else{
				send_fatal_error($I['nodb']);
			}
		}
	}
	if(MEMCACHED){
		if(!extension_loaded('memcached')){
			send_fatal_error($I['memcachedextrequired']);
		}
		$memcached=new Memcached();
		$memcached->addServer(MEMCACHEDHOST, MEMCACHEDPORT);
	}
	if(!isset($_REQUEST['action']) || $_REQUEST['action']==='setup'){
		if(!check_init()){
			send_init();
		}
		update_db();
	}elseif($_REQUEST['action']==='init'){
		init_chat();
	}
}

