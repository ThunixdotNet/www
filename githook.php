<?php

/* gitea deploy webhook */

/* security */
$access_token = '1234567890';
$lastrun = '/tmp/ansible-hook-last-run';

/* get json data */
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$client_token = $data["secret"];
if ($client_token !== $access_token)
{
    http_response_code(403); 
    exit(0);
}

syslog(LOG_INFO, 'Ansible Webhook recieved.');

//* if you need get full json input */
//fwrite($fs, 'DATA: '.print_r($data, true).PHP_EOL);


if (time()-filemtime($lastrun) > 300) {
	exec("/etc/cron.hourly/ansible-pull");
	touch ($lastrun);
	echo "Ansible webhook recieved.";
	}
	else {
	http_response_code(429);
	exit(0);
	}
?>

