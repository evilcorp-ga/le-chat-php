<?php
function load_config(){
	mb_internal_encoding('UTF-8');
	define('VERSION', '1.23.4'); // Script version
	define('DBVERSION', 41); // Database layout version
	define('MSGENCRYPTED', false); // Store messages encrypted in the database to prevent other database users from reading them - true/false - visit the setup page after editing!
	define('ENCRYPTKEY', 'MY_KEY'); // Encryption key for messages
	define('DBHOST', 'localhost'); // Database host
	define('DBUSER', 'www-data'); // Database user
	define('DBPASS', 'YOUR_DB_PASS'); // Database password
	define('DBNAME', 'public_chat'); // Database
	define('PERSISTENT', true); // Use persistent database conection true/false
	define('PREFIX', ''); // Prefix - Set this to a unique value for every chat, if you have more than 1 chats on the same database or domain - use only alpha-numeric values (A-Z, a-z, 0-9, or _) other symbols might break the queries
	define('MEMCACHED', false); // Enable/disable memcached caching true/false - needs memcached extension and a memcached server.
	if(MEMCACHED){
		define('MEMCACHEDHOST', 'localhost'); // Memcached host
		define('MEMCACHEDPORT', '11211'); // Memcached port
	}
	define('DBDRIVER', 2); // Selects the database driver to use - 0=MySQL, 1=PostgreSQL, 2=sqlite
	if(DBDRIVER===2){
		define('SQLITEDBFILE', 'lechat.db'); // Filepath of the sqlite database, if sqlite is used - make sure it is writable for the webserver user
	}
	define('COOKIENAME', PREFIX . 'chat_session'); // Cookie name storing the session information
	define('LANG', 'en'); // Default language
}

