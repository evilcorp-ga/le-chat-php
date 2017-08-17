<?php
function update_db(){
	global $I, $db, $memcached;
	$dbversion=(int) get_setting('dbversion');
	$msgencrypted=(bool) get_setting('msgencrypted');
	if($dbversion>=DBVERSION && $msgencrypted===MSGENCRYPTED){
		return;
	}
	ignore_user_abort(true);
	set_time_limit(0);
	if(DBDRIVER===0){//MySQL
		$memengine=' ENGINE=MEMORY';
		$diskengine=' ENGINE=InnoDB';
		$charset=' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin';
		$primary='integer PRIMARY KEY AUTO_INCREMENT';
		$longtext='longtext';
	}elseif(DBDRIVER===1){//PostgreSQL
		$memengine='';
		$diskengine='';
		$charset='';
		$primary='serial PRIMARY KEY';
		$longtext='text';
	}else{//SQLite
		$memengine='';
		$diskengine='';
		$charset='';
		$primary='integer PRIMARY KEY';
		$longtext='text';
	}
	$msg='';
	if($dbversion<2){
		$db->exec('CREATE TABLE IF NOT EXISTS ' . PREFIX . "ignored (id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, ignored varchar(50) NOT NULL, `by` varchar(50) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}
	if($dbversion<3){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('rulestxt', '');");
	}
	if($dbversion<4){
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD incognito smallint NOT NULL;');
	}
	if($dbversion<5){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('globalpass', '');");
	}
	if($dbversion<6){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('dateformat', 'm-d H:i:s');");
	}
	if($dbversion<7){
		$db->exec('ALTER TABLE ' . PREFIX . 'captcha ADD code char(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
	}
	if($dbversion<8){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('captcha', '0'), ('englobalpass', '0');");
		$ga=(int) get_setting('guestaccess');
		if($ga===-1){
			update_setting('guestaccess', 0);
			update_setting('englobalpass', 1);
		}elseif($ga===4){
			update_setting('guestaccess', 1);
			update_setting('englobalpass', 2);
		}
	}
	if($dbversion<9){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting,value) VALUES ('msgencrypted', '0');");
		$db->exec('ALTER TABLE ' . PREFIX . 'settings MODIFY value varchar(20000) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'messages DROP postid;');
	}
	if($dbversion<10){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('css', ''), ('memberexpire', '60'), ('guestexpire', '15'), ('kickpenalty', '10'), ('entrywait', '120'), ('messageexpire', '14400'), ('messagelimit', '150'), ('maxmessage', 2000), ('captchatime', '600');");
	}
	if($dbversion<11){
		$db->exec('ALTER TABLE ' , PREFIX . 'captcha CHARACTER SET utf8 COLLATE utf8_bin;');
		$db->exec('ALTER TABLE ' . PREFIX . 'filter CHARACTER SET utf8 COLLATE utf8_bin;');
		$db->exec('ALTER TABLE ' . PREFIX . 'ignored CHARACTER SET utf8 COLLATE utf8_bin;');
		$db->exec('ALTER TABLE ' . PREFIX . 'messages CHARACTER SET utf8 COLLATE utf8_bin;');
		$db->exec('ALTER TABLE ' . PREFIX . 'notes CHARACTER SET utf8 COLLATE utf8_bin;');
		$db->exec('ALTER TABLE ' . PREFIX . 'settings CHARACTER SET utf8 COLLATE utf8_bin;');
		$db->exec('CREATE TABLE ' . PREFIX . "linkfilter (id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, `match` varchar(255) NOT NULL, `replace` varchar(255) NOT NULL, regex smallint NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_bin;");
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD style varchar(255) NOT NULL;');
		$result=$db->query('SELECT * FROM ' . PREFIX . 'members;');
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'members SET style=? WHERE id=?;');
		$F=load_fonts();
		while($temp=$result->fetch(PDO::FETCH_ASSOC)){
			$style="color:#$temp[colour];";
			if(isset($F[$temp['fontface']])){
				$style.=$F[$temp['fontface']];
			}
			if(strpos($temp['fonttags'], 'i')!==false){
				$style.='font-style:italic;';
			}
			if(strpos($temp['fonttags'], 'b')!==false){
				$style.='font-weight:bold;';
			}
			$stmt->execute([$style, $temp['id']]);
		}
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('colbg', '000000'), ('coltxt', 'FFFFFF'), ('maxname', '20'), ('minpass', '5'), ('defaultrefresh', '20'), ('dismemcaptcha', '0'), ('suguests', '0'), ('imgembed', '1'), ('timestamps', '1'), ('trackip', '0'), ('captchachars', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), ('memkick', '1'), ('forceredirect', '0'), ('redirect', ''), ('incognito', '1');");
	}
	if($dbversion<12){
		$db->exec('ALTER TABLE ' . PREFIX . 'captcha MODIFY code char(5) NOT NULL, DROP INDEX id, ADD PRIMARY KEY (id) USING BTREE;');
		$db->exec('ALTER TABLE ' . PREFIX . 'captcha ENGINE=MEMORY;');
		$db->exec('ALTER TABLE ' . PREFIX . 'filter MODIFY id integer unsigned NOT NULL AUTO_INCREMENT, MODIFY `match` varchar(255) NOT NULL, MODIFY replace varchar(20000) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'ignored MODIFY ignored varchar(50) NOT NULL, MODIFY `by` varchar(50) NOT NULL, ADD INDEX(ignored), ADD INDEX(`by`);');
		$db->exec('ALTER TABLE ' . PREFIX . 'linkfilter MODIFY match varchar(255) NOT NULL, MODIFY replace varchar(255) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'messages MODIFY poster varchar(50) NOT NULL, MODIFY recipient varchar(50) NOT NULL, MODIFY text varchar(20000) NOT NULL, ADD INDEX(poster), ADD INDEX(recipient), ADD INDEX(postdate), ADD INDEX(poststatus);');
		$db->exec('ALTER TABLE ' . PREFIX . 'notes MODIFY type char(5) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL, MODIFY editedby varchar(50) NOT NULL, MODIFY text varchar(20000) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'settings MODIFY id integer unsigned NOT NULL, MODIFY setting varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL, MODIFY value varchar(20000) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'settings DROP PRIMARY KEY, DROP id, ADD PRIMARY KEY(setting);');
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('chatname', 'My Chat'), ('topic', ''), ('msgsendall', '$I[sendallmsg]'), ('msgsendmem', '$I[sendmemmsg]'), ('msgsendmod', '$I[sendmodmsg]'), ('msgsendadm', '$I[sendadmmsg]'), ('msgsendprv', '$I[sendprvmsg]'), ('numnotes', '3');");
	}
	if($dbversion<13){
		$db->exec('ALTER TABLE ' . PREFIX . 'filter CHANGE `match` filtermatch varchar(255) NOT NULL, CHANGE `replace` filterreplace varchar(20000) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'ignored CHANGE ignored ign varchar(50) NOT NULL, CHANGE `by` ignby varchar(50) NOT NULL;');
		$db->exec('ALTER TABLE ' . PREFIX . 'linkfilter CHANGE `match` filtermatch varchar(255) NOT NULL, CHANGE `replace` filterreplace varchar(255) NOT NULL;');
	}
	if($dbversion<14){
		if(MEMCACHED){
			$memcached->delete(DBNAME . '-' . PREFIX . 'members');
			$memcached->delete(DBNAME . '-' . PREFIX . 'ignored');
		}
		if(DBDRIVER===0){//MySQL - previously had a wrong SQL syntax and the captcha table was not created.
			$db->exec('CREATE TABLE IF NOT EXISTS ' . PREFIX . 'captcha (id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, time integer unsigned NOT NULL, code char(5) NOT NULL) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
		}
	}
	if($dbversion<15){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('mailsender', 'www-data <www-data@localhost>'), ('mailreceiver', 'Webmaster <webmaster@localhost>'), ('sendmail', '0'), ('modfallback', '1'), ('guestreg', '0');");
	}
	if($dbversion<17){
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD COLUMN nocache smallint NOT NULL DEFAULT 0;');
	}
	if($dbversion<18){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('disablepm', '0');");
	}
	if($dbversion<19){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('disabletext', '<h1>$I[disabledtext]</h1>');");
	}
	if($dbversion<20){
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD COLUMN tz smallint NOT NULL DEFAULT 0;');
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('defaulttz', 'UTC');");
	}
	if($dbversion<21){
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD COLUMN eninbox smallint NOT NULL DEFAULT 0;');
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('eninbox', '0');");
		if(DBDRIVER===0){
			$db->exec('CREATE TABLE ' . PREFIX . "inbox (id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT, postid integer unsigned NOT NULL, postdate integer unsigned NOT NULL, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text varchar(20000) NOT NULL, INDEX(postid), INDEX(poster), INDEX(recipient)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
		}else{
			$db->exec('CREATE TABLE ' . PREFIX . "inbox (id $primary, postdate integer NOT NULL, postid integer NOT NULL, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text varchar(20000) NOT NULL);");
			$db->exec('CREATE INDEX ' . PREFIX . 'inbox_postid ON ' . PREFIX . 'inbox(postid);');
			$db->exec('CREATE INDEX ' . PREFIX . 'inbox_poster ON ' . PREFIX . 'inbox(poster);');
			$db->exec('CREATE INDEX ' . PREFIX . 'inbox_recipient ON ' . PREFIX . 'inbox(recipient);');
		}
	}
	if($dbversion<23){
		$db->exec('DELETE FROM ' . PREFIX . "settings WHERE setting='enablejs';");
	}
	if($dbversion<25){
		$db->exec('DELETE FROM ' . PREFIX . "settings WHERE setting='keeplimit';");
	}
	if($dbversion<26){
		$db->exec('INSERT INTO ' . PREFIX . 'settings (setting, value) VALUES (\'passregex\', \'.*\'), (\'nickregex\', \'^[A-Za-z0-9]*$\');');
	}
	if($dbversion<27){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('externalcss', '');");
	}
	if($dbversion<28){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('enablegreeting', '0');");
	}
	if($dbversion<29){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('sortupdown', '0');");
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD COLUMN sortupdown smallint NOT NULL DEFAULT 0;');
	}
	if($dbversion<30){
		$db->exec('ALTER TABLE ' . PREFIX . 'filter ADD COLUMN cs smallint NOT NULL DEFAULT 0;');
		if(MEMCACHED){
			$memcached->delete(DBNAME . '-' . PREFIX . "filter");
		}
	}
	if($dbversion<31){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('hidechatters', '0');");
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD COLUMN hidechatters smallint NOT NULL DEFAULT 0;');
	}
	if($dbversion<32 && DBDRIVER===0){
		//recreate db in utf8mb4
		try{
			$olddb=new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::ATTR_PERSISTENT=>PERSISTENT]);
		}catch(PDOException $e){
			send_fatal_error($I['nodb']);
		}
		$db->exec('DROP TABLE ' . PREFIX . 'captcha;');
		$db->exec('CREATE TABLE ' . PREFIX . "captcha (id integer PRIMARY KEY AUTO_INCREMENT, time integer NOT NULL, code char(5) NOT NULL) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$result=$olddb->query('SELECT filtermatch, filterreplace, allowinpm, regex, kick, cs FROM ' . PREFIX . 'filter;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'filter;');
		$db->exec('CREATE TABLE ' . PREFIX . "filter (id integer PRIMARY KEY AUTO_INCREMENT, filtermatch varchar(255) NOT NULL, filterreplace text NOT NULL, allowinpm smallint NOT NULL, regex smallint NOT NULL, kick smallint NOT NULL, cs smallint NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'filter (filtermatch, filterreplace, allowinpm, regex, kick, cs) VALUES(?, ?, ?, ?, ?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$result=$olddb->query('SELECT ign, ignby FROM ' . PREFIX . 'ignored;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'ignored;');
		$db->exec('CREATE TABLE ' . PREFIX . "ignored (id integer PRIMARY KEY AUTO_INCREMENT, ign varchar(50) NOT NULL, ignby varchar(50) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'ignored (ign, ignby) VALUES(?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$db->exec('CREATE INDEX ' . PREFIX . 'ign ON ' . PREFIX . 'ignored(ign);');
		$db->exec('CREATE INDEX ' . PREFIX . 'ignby ON ' . PREFIX . 'ignored(ignby);');
		$result=$olddb->query('SELECT postdate, postid, poster, recipient, text FROM ' . PREFIX . 'inbox;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'inbox;');
		$db->exec('CREATE TABLE ' . PREFIX . "inbox (id integer PRIMARY KEY AUTO_INCREMENT, postdate integer NOT NULL, postid integer NOT NULL UNIQUE, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'inbox (postdate, postid, poster, recipient, text) VALUES(?, ?, ?, ?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$db->exec('CREATE INDEX ' . PREFIX . 'inbox_poster ON ' . PREFIX . 'inbox(poster);');
		$db->exec('CREATE INDEX ' . PREFIX . 'inbox_recipient ON ' . PREFIX . 'inbox(recipient);');
		$result=$olddb->query('SELECT filtermatch, filterreplace, regex FROM ' . PREFIX . 'linkfilter;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'linkfilter;');
		$db->exec('CREATE TABLE ' . PREFIX . "linkfilter (id integer PRIMARY KEY AUTO_INCREMENT, filtermatch varchar(255) NOT NULL, filterreplace varchar(255) NOT NULL, regex smallint NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'linkfilter (filtermatch, filterreplace, regex) VALUES(?, ?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$result=$olddb->query('SELECT nickname, passhash, status, refresh, bgcolour, regedby, lastlogin, timestamps, embed, incognito, style, nocache, tz, eninbox, sortupdown, hidechatters FROM ' . PREFIX . 'members;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'members;');
		$db->exec('CREATE TABLE ' . PREFIX . "members (id integer PRIMARY KEY AUTO_INCREMENT, nickname varchar(50) NOT NULL UNIQUE, passhash char(32) NOT NULL, status smallint NOT NULL, refresh smallint NOT NULL, bgcolour char(6) NOT NULL, regedby varchar(50) DEFAULT '', lastlogin integer DEFAULT 0, timestamps smallint NOT NULL, embed smallint NOT NULL, incognito smallint NOT NULL, style varchar(255) NOT NULL, nocache smallint NOT NULL, tz smallint NOT NULL, eninbox smallint NOT NULL, sortupdown smallint NOT NULL, hidechatters smallint NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'members (nickname, passhash, status, refresh, bgcolour, regedby, lastlogin, timestamps, embed, incognito, style, nocache, tz, eninbox, sortupdown, hidechatters) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$result=$olddb->query('SELECT postdate, poststatus, poster, recipient, text, delstatus FROM ' . PREFIX . 'messages;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'messages;');
		$db->exec('CREATE TABLE ' . PREFIX . "messages (id integer PRIMARY KEY AUTO_INCREMENT, postdate integer NOT NULL, poststatus smallint NOT NULL, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text text NOT NULL, delstatus smallint NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'messages (postdate, poststatus, poster, recipient, text, delstatus) VALUES(?, ?, ?, ?, ?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$db->exec('CREATE INDEX ' . PREFIX . 'poster ON ' . PREFIX . 'messages (poster);');
		$db->exec('CREATE INDEX ' . PREFIX . 'recipient ON ' . PREFIX . 'messages(recipient);');
		$db->exec('CREATE INDEX ' . PREFIX . 'postdate ON ' . PREFIX . 'messages(postdate);');
		$db->exec('CREATE INDEX ' . PREFIX . 'poststatus ON ' . PREFIX . 'messages(poststatus);');
		$result=$olddb->query('SELECT type, lastedited, editedby, text FROM ' . PREFIX . 'notes;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'notes;');
		$db->exec('CREATE TABLE ' . PREFIX . "notes (id integer PRIMARY KEY AUTO_INCREMENT, type char(5) NOT NULL, lastedited integer NOT NULL, editedby varchar(50) NOT NULL, text text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'notes (type, lastedited, editedby, text) VALUES(?, ?, ?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$result=$olddb->query('SELECT setting, value FROM ' . PREFIX . 'settings;');
		$data=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'settings;');
		$db->exec('CREATE TABLE ' . PREFIX . "settings (setting varchar(50) NOT NULL PRIMARY KEY, value text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'settings (setting, value) VALUES(?, ?);');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
	}
	if($dbversion<33){
		$db->exec('CREATE TABLE ' . PREFIX . "files (id $primary, postid integer NOT NULL UNIQUE, filename varchar(255) NOT NULL, hash char(40) NOT NULL, type varchar(255) NOT NULL, data $longtext NOT NULL)$diskengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'files_hash ON ' . PREFIX . 'files(hash);');
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('enfileupload', '0'), ('msgattache', '%2\$s [%1\$s]'), ('maxuploadsize', '1024');");
	}
	if($dbversion<34){
		$msg.="<br>$I[cssupdate]";
		$db->exec('ALTER TABLE ' . PREFIX . 'members ADD COLUMN nocache_old smallint NOT NULL DEFAULT 0;');
	}
	if($dbversion<37){
		$db->exec('ALTER TABLE ' . PREFIX . 'members MODIFY tz varchar(255) NOT NULL;');
		$db->exec('UPDATE ' . PREFIX . "members SET tz='UTC';");
		$db->exec('UPDATE ' . PREFIX . "settings SET value='UTC' WHERE setting='defaulttz';");
	}
	if($dbversion<38){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('nextcron', '0');");
		$db->exec('DELETE FROM ' . PREFIX . 'inbox WHERE recipient NOT IN (SELECT nickname FROM ' . PREFIX . 'members);'); // delete inbox of members who deleted themselves
	}
	if($dbversion<39){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('personalnotes', '1');");
		$result=$db->query('SELECT type, id FROM ' . PREFIX . 'notes;');
		while($tmp=$result->fetch(PDO::FETCH_NUM)){
			if($tmp[0]==='admin'){
				$tmp[0]=0;
			}else{
				$tmp[0]=1;
			}
			$data[]=$tmp;
		}
		$db->exec('ALTER TABLE ' . PREFIX . 'notes MODIFY type smallint NOT NULL;');
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'notes SET type=? WHERE id=?;');
		foreach($data as $tmp){
			$stmt->execute($tmp);
		}
		$db->exec('CREATE INDEX ' . PREFIX . 'notes_type ON ' . PREFIX . 'notes(type);');
		$db->exec('CREATE INDEX ' . PREFIX . 'notes_editedby ON ' . PREFIX . 'notes(editedby);');
	}
	if($dbversion<40){
		$db->exec('INSERT INTO ' . PREFIX . "settings (setting, value) VALUES ('filtermodkick', '1');");
	}
	if($dbversion<41){
		$db->exec('DROP TABLE ' . PREFIX . 'sessions;');
		$db->exec('CREATE TABLE ' . PREFIX . "sessions (id $primary, session char(32) NOT NULL UNIQUE, nickname varchar(50) NOT NULL UNIQUE, status smallint NOT NULL, refresh smallint NOT NULL, style varchar(255) NOT NULL, lastpost integer NOT NULL, passhash varchar(255) NOT NULL, postid char(6) NOT NULL DEFAULT '000000', useragent varchar(255) NOT NULL, kickmessage varchar(255) DEFAULT '', bgcolour char(6) NOT NULL, entry integer NOT NULL, timestamps smallint NOT NULL, embed smallint NOT NULL, incognito smallint NOT NULL, ip varchar(45) NOT NULL, nocache smallint NOT NULL, tz varchar(255) NOT NULL, eninbox smallint NOT NULL, sortupdown smallint NOT NULL, hidechatters smallint NOT NULL, nocache_old smallint NOT NULL)$memengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'status ON ' . PREFIX . 'sessions(status);');
		$db->exec('CREATE INDEX ' . PREFIX . 'lastpost ON ' . PREFIX . 'sessions(lastpost);');
		$db->exec('CREATE INDEX ' . PREFIX . 'incognito ON ' . PREFIX . 'sessions(incognito);');
		$result=$db->query('SELECT nickname, passhash, status, refresh, bgcolour, regedby, lastlogin, timestamps, embed, incognito, style, nocache, nocache_old, tz, eninbox, sortupdown, hidechatters FROM ' . PREFIX . 'members;');
		$members=$result->fetchAll(PDO::FETCH_NUM);
		$result=$db->query('SELECT postdate, postid, poster, recipient, text FROM ' . PREFIX . 'inbox;');
		$inbox=$result->fetchAll(PDO::FETCH_NUM);
		$db->exec('DROP TABLE ' . PREFIX . 'inbox;');
		$db->exec('DROP TABLE ' . PREFIX . 'members;');
		$db->exec('CREATE TABLE ' . PREFIX . "members (id $primary, nickname varchar(50) NOT NULL UNIQUE, passhash varchar(255) NOT NULL, status smallint NOT NULL, refresh smallint NOT NULL, bgcolour char(6) NOT NULL, regedby varchar(50) DEFAULT '', lastlogin integer DEFAULT 0, timestamps smallint NOT NULL, embed smallint NOT NULL, incognito smallint NOT NULL, style varchar(255) NOT NULL, nocache smallint NOT NULL, nocache_old smallint NOT NULL, tz varchar(255) NOT NULL, eninbox smallint NOT NULL, sortupdown smallint NOT NULL, hidechatters smallint NOT NULL)$diskengine$charset");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'members (nickname, passhash, status, refresh, bgcolour, regedby, lastlogin, timestamps, embed, incognito, style, nocache, nocache_old, tz, eninbox, sortupdown, hidechatters) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		foreach($members as $tmp){
			$stmt->execute($tmp);
		}
		$db->exec('CREATE TABLE ' . PREFIX . "inbox (id $primary, postdate integer NOT NULL, postid integer NOT NULL UNIQUE, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text text NOT NULL)$diskengine$charset;");
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'inbox (postdate, postid, poster, recipient, text) VALUES(?, ?, ?, ?, ?);');
		foreach($inbox as $tmp){
			$stmt->execute($tmp);
		}
		$db->exec('CREATE INDEX ' . PREFIX . 'inbox_poster ON ' . PREFIX . 'inbox(poster);');
		$db->exec('CREATE INDEX ' . PREFIX . 'inbox_recipient ON ' . PREFIX . 'inbox(recipient);');
		$db->exec('ALTER TABLE ' . PREFIX . 'inbox ADD FOREIGN KEY (recipient) REFERENCES ' . PREFIX . 'members(nickname) ON DELETE CASCADE ON UPDATE CASCADE;');
	}
	update_setting('dbversion', DBVERSION);
	if($msgencrypted!==MSGENCRYPTED){
		if(!extension_loaded('openssl')){
			send_fatal_error($I['opensslextrequired']);
		}
		$result=$db->query('SELECT id, text FROM ' . PREFIX . 'messages;');
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'messages SET text=? WHERE id=?;');
		while($message=$result->fetch(PDO::FETCH_ASSOC)){
			if(MSGENCRYPTED){
				$message['text']=openssl_encrypt($message['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
			}else{
				$message['text']=openssl_decrypt($message['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
			}
			$stmt->execute([$message['text'], $message['id']]);
		}
		$result=$db->query('SELECT id, text FROM ' . PREFIX . 'notes;');
		$stmt=$db->prepare('UPDATE ' . PREFIX . 'notes SET text=? WHERE id=?;');
		while($message=$result->fetch(PDO::FETCH_ASSOC)){
			if(MSGENCRYPTED){
				$message['text']=openssl_encrypt($message['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
			}else{
				$message['text']=openssl_decrypt($message['text'], 'aes-256-cbc', ENCRYPTKEY, 0, '1234567890123456');
			}
			$stmt->execute([$message['text'], $message['id']]);
		}
		update_setting('msgencrypted', (int) MSGENCRYPTED);
	}
	send_update($msg);
}

