<?php

/* gitea deploy webhook */

/* security and environment*/
$access_token     = '1234567890';
$ansible_lastrun  = '/dev/shm/ansible-hook-last-run';
$ansible_dropfile = '/dev/shm/run-ansible';
$www_lastrun      = '/dev/shm/www-hook-last-run';
$www_dropfile     = '/dev/shm/run-www';
$remoteip         = $_SERVER['REMOTE_ADDR'];
$allowedip        = '195.201.242.48';
$ratelimit        = 300;

/* get json data */
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$client_token = $data["secret"];
if ($client_token !== $access_token)
{
    http_response_code(403); 
    echo "HTTP 403 - Forbidden.";
    exit(0);
}

if ($remoteip !== $allowedip)
{
    http_repsonse_code(403);
    echo "HTTP 403 - Forbidden.";
    exit(0);
}

//* if you need get full json input */
//fwrite($fs, 'DATA: '.print_r($data, true).PHP_EOL);

if ($data["repository"]["full_name"] == 'thunix/www') {
	syslog(LOG_INFO, 'Ansible Webhook recieved.');
	if ( time () - filemtime ( $ansible_lastrun ) > $ratelimit ) {
		touch ( $ansible_dropfile );
		touch ( $ansible_lastrun );
		echo "HTTP 200 - Ansible webhook recieved.";
		}
	else {
		http_response_code(429);
		echo "HTTP 429 - Rate Limited.";
		exit(0);
		}
}
elseif ($data["repository"]["full_name"] == 'thunix/www') {
	syslog(LOG_INFO, 'WWW Webhook recieved.');
	if ( time () - filemtime ( $www_lastrun ) > $ratelimit ) {
		touch ( $www_dropfile );
		touch ( $www_lastrun );
		http_response_code(200);
		echo "HTTP 200 - WWW webhook recieved.";
		} 
	else {
		http_response_code(429);
		echo "HTTP 429 - Rate Limited.";
		exit(0);
		}
	} 
else {
	http_response_code(418);
	echo "HTTP 418 - I'm a teapot.";
	exit(0);
	}
?>

