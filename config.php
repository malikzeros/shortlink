<?php
define('DB_NAME', 'shortlink');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_TABLE', 'shortenedurls');

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');

define('LIMIT_TO_IP', $_SERVER['REMOTE_ADDR']);

define('TRACK', FALSE);

define('CHECK_URL', FALSE);

define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

define('CACHE', TRUE);

define('CACHE_DIR', dirname(__FILE__) . '/cache/');
