<?php
function init_chat(){
	global $I, $db;
	$suwrite='';
	if(check_init()){
		$suwrite=$I['initdbexist'];
		$result=$db->query('SELECT null FROM ' . PREFIX . 'members WHERE status=8;');
		if($result->fetch(PDO::FETCH_NUM)){
			$suwrite=$I['initsuexist'];
		}
	}elseif(!preg_match('/^[a-z0-9]{1,20}$/i', $_REQUEST['sunick'])){
		$suwrite=sprintf($I['invalnick'], 20, '^[A-Za-z1-9]*$');
	}elseif(mb_strlen($_REQUEST['supass'])<5){
		$suwrite=sprintf($I['invalpass'], 5, '.*');
	}elseif($_REQUEST['supass']!==$_REQUEST['supassc']){
		$suwrite=$I['noconfirm'];
	}else{
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
		$db->exec('CREATE TABLE ' . PREFIX . "captcha (id $primary, time integer NOT NULL, code char(5) NOT NULL)$memengine$charset;");
		$db->exec('CREATE TABLE ' . PREFIX . "files (id $primary, postid integer NOT NULL UNIQUE, filename varchar(255) NOT NULL, hash char(40) NOT NULL, type varchar(255) NOT NULL, data $longtext NOT NULL)$diskengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'files_hash ON ' . PREFIX . 'files(hash);');
		$db->exec('CREATE TABLE ' . PREFIX . "filter (id $primary, filtermatch varchar(255) NOT NULL, filterreplace text NOT NULL, allowinpm smallint NOT NULL, regex smallint NOT NULL, kick smallint NOT NULL, cs smallint NOT NULL)$diskengine$charset;");
		$db->exec('CREATE TABLE ' . PREFIX . "ignored (id $primary, ign varchar(50) NOT NULL, ignby varchar(50) NOT NULL)$diskengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'ign ON ' . PREFIX . 'ignored(ign);');
		$db->exec('CREATE INDEX ' . PREFIX . 'ignby ON ' . PREFIX . 'ignored(ignby);');
		$db->exec('CREATE TABLE ' . PREFIX . "inbox (id $primary, postdate integer NOT NULL, postid integer NOT NULL UNIQUE, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text text NOT NULL)$diskengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'inbox_poster ON ' . PREFIX . 'inbox(poster);');
		$db->exec('CREATE INDEX ' . PREFIX . 'inbox_recipient ON ' . PREFIX . 'inbox(recipient);');
		$db->exec('CREATE TABLE ' . PREFIX . "linkfilter (id $primary, filtermatch varchar(255) NOT NULL, filterreplace varchar(255) NOT NULL, regex smallint NOT NULL)$diskengine$charset;");
		$db->exec('CREATE TABLE ' . PREFIX . "members (id $primary, nickname varchar(50) NOT NULL UNIQUE, passhash varchar(255) NOT NULL, status smallint NOT NULL, refresh smallint NOT NULL, bgcolour char(6) NOT NULL, regedby varchar(50) DEFAULT '', lastlogin integer DEFAULT 0, timestamps smallint NOT NULL, embed smallint NOT NULL, incognito smallint NOT NULL, style varchar(255) NOT NULL, nocache smallint NOT NULL, tz varchar(255) NOT NULL, eninbox smallint NOT NULL, sortupdown smallint NOT NULL, hidechatters smallint NOT NULL, nocache_old smallint NOT NULL)$diskengine$charset;");
		$db->exec('ALTER TABLE ' . PREFIX . 'inbox ADD FOREIGN KEY (recipient) REFERENCES ' . PREFIX . 'members(nickname) ON DELETE CASCADE ON UPDATE CASCADE;');
		$db->exec('CREATE TABLE ' . PREFIX . "messages (id $primary, postdate integer NOT NULL, poststatus smallint NOT NULL, poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text text NOT NULL, delstatus smallint NOT NULL)$diskengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'poster ON ' . PREFIX . 'messages (poster);');
		$db->exec('CREATE INDEX ' . PREFIX . 'recipient ON ' . PREFIX . 'messages(recipient);');
		$db->exec('CREATE INDEX ' . PREFIX . 'postdate ON ' . PREFIX . 'messages(postdate);');
		$db->exec('CREATE INDEX ' . PREFIX . 'poststatus ON ' . PREFIX . 'messages(poststatus);');
		$db->exec('CREATE TABLE ' . PREFIX . "notes (id $primary, type smallint NOT NULL, lastedited integer NOT NULL, editedby varchar(50) NOT NULL, text text NOT NULL)$diskengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'notes_type ON ' . PREFIX . 'notes(type);');
		$db->exec('CREATE INDEX ' . PREFIX . 'notes_editedby ON ' . PREFIX . 'notes(editedby);');
		$db->exec('CREATE TABLE ' . PREFIX . "sessions (id $primary, session char(32) NOT NULL UNIQUE, nickname varchar(50) NOT NULL UNIQUE, status smallint NOT NULL, refresh smallint NOT NULL, style varchar(255) NOT NULL, lastpost integer NOT NULL, passhash varchar(255) NOT NULL, postid char(6) NOT NULL DEFAULT '000000', useragent varchar(255) NOT NULL, kickmessage varchar(255) DEFAULT '', bgcolour char(6) NOT NULL, entry integer NOT NULL, timestamps smallint NOT NULL, embed smallint NOT NULL, incognito smallint NOT NULL, ip varchar(45) NOT NULL, nocache smallint NOT NULL, tz varchar(255) NOT NULL, eninbox smallint NOT NULL, sortupdown smallint NOT NULL, hidechatters smallint NOT NULL, nocache_old smallint NOT NULL)$memengine$charset;");
		$db->exec('CREATE INDEX ' . PREFIX . 'status ON ' . PREFIX . 'sessions(status);');
		$db->exec('CREATE INDEX ' . PREFIX . 'lastpost ON ' . PREFIX . 'sessions(lastpost);');
		$db->exec('CREATE INDEX ' . PREFIX . 'incognito ON ' . PREFIX . 'sessions(incognito);');
		$db->exec('CREATE TABLE ' . PREFIX . "settings (setting varchar(50) NOT NULL PRIMARY KEY, value text NOT NULL)$diskengine$charset;");

		$settings=[
			['guestaccess', '0'],
			['globalpass', ''],
			['englobalpass', '0'],
			['captcha', '0'],
			['dateformat', 'm-d H:i:s'],
			['rulestxt', ''],
			['msgencrypted', '0'],
			['dbversion', DBVERSION],
			['css', ''],
			['memberexpire', '60'],
			['guestexpire', '15'],
			['kickpenalty', '10'],
			['entrywait', '120'],
			['messageexpire', '14400'],
			['messagelimit', '150'],
			['maxmessage', 2000],
			['captchatime', '600'],
			['colbg', '000000'],
			['coltxt', 'FFFFFF'],
			['maxname', '20'],
			['minpass', '5'],
			['defaultrefresh', '20'],
			['dismemcaptcha', '0'],
			['suguests', '0'],
			['imgembed', '1'],
			['timestamps', '1'],
			['trackip', '0'],
			['captchachars', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'],
			['memkick', '1'],
			['forceredirect', '0'],
			['redirect', ''],
			['incognito', '1'],
			['chatname', 'My Chat'],
			['topic', ''],
			['msgsendall', $I['sendallmsg']],
			['msgsendmem', $I['sendmemmsg']],
			['msgsendmod', $I['sendmodmsg']],
			['msgsendadm', $I['sendadmmsg']],
			['msgsendprv', $I['sendprvmsg']],
			['msgenter', $I['entermsg']],
			['msgexit', $I['exitmsg']],
			['msgmemreg', $I['memregmsg']],
			['msgsureg', $I['suregmsg']],
			['msgkick', $I['kickmsg']],
			['msgmultikick', $I['multikickmsg']],
			['msgallkick', $I['allkickmsg']],
			['msgclean', $I['cleanmsg']],
			['numnotes', '3'],
			['mailsender', 'www-data <www-data@localhost>'],
			['mailreceiver', 'Webmaster <webmaster@localhost>'],
			['sendmail', '0'],
			['modfallback', '1'],
			['guestreg', '0'],
			['disablepm', '0'],
			['disabletext', "<h1>$I[disabledtext]</h1>"],
			['defaulttz', 'UTC'],
			['eninbox', '0'],
			['passregex', '.*'],
			['nickregex', '^[A-Za-z0-9]*$'],
			['externalcss', ''],
			['enablegreeting', '0'],
			['sortupdown', '0'],
			['hidechatters', '0'],
			['enfileupload', '0'],
			['msgattache', '%2$s [%1$s]'],
			['maxuploadsize', '1024'],
			['nextcron', '0'],
			['personalnotes', '1'],
		];
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'settings (setting, value) VALUES (?, ?);');
		foreach($settings as $pair){
			$stmt->execute($pair);
		}
		$reg=[
			'nickname'	=>$_REQUEST['sunick'],
			'passhash'	=>password_hash($_REQUEST['supass'], PASSWORD_DEFAULT),
			'status'	=>8,
			'refresh'	=>20,
			'bgcolour'	=>'000000',
			'timestamps'	=>1,
			'style'		=>'color:#FFFFFF;',
			'embed'		=>1,
			'incognito'	=>0,
			'nocache'	=>0,
			'nocache_old'	=>1,
			'tz'		=>'UTC',
			'eninbox'	=>0,
			'sortupdown'	=>0,
			'hidechatters'	=>0,
			'filtermodkick'	=>1,
		];
		$stmt=$db->prepare('INSERT INTO ' . PREFIX . 'members (nickname, passhash, status, refresh, bgcolour, timestamps, style, embed, incognito, nocache, tz, eninbox, sortupdown, hidechatters, nocache_old) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute([$reg['nickname'], $reg['passhash'], $reg['status'], $reg['refresh'], $reg['bgcolour'], $reg['timestamps'], $reg['style'], $reg['embed'], $reg['incognito'], $reg['nocache'], $reg['tz'], $reg['eninbox'], $reg['sortupdown'], $reg['hidechatters'], $reg['nocache_old']]);
		$suwrite=$I['susuccess'];
	}
	print_start('init');
	echo "<h2>$I[init]</h2><br><h3>$I[sulogin]</h3>$suwrite<br><br><br>";
	echo form('setup').submit($I['initgosetup']).'</form>'.credit();
	print_end();
}

