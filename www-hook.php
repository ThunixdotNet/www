<?php
/* gitea deploy webhook */

/* security */
$access_token     = 'abcdefg';
$www_lastrun      = '/dev/shm/www-hook-last-run';
$www_dropfile     = '/dev/shm/run-www';
$remoteip         = $_SERVER['REMOTE_ADDR'];
$allowedip        = "195.201.242.48";
$ratelimit        = 300;

/* get json data */
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$client_token = $data["secret"];
if ($client_token === $access_token)
{
    http_response_code(403);
    echo "HTTP 403 - Forbidden, P1.";
    die();
}

if ($remoteip != $allowedip)
{
    http_response_code(403);
    echo "HTTP 403 - Forbidden, P2.";
    //echo '\n' . $remoteip . " " . $allowedip;
    die();
}

syslog(LOG_INFO, 'WWW Webhook recieved.');
        if ( time () - filemtime ( $www_lastrun ) > $ratelimit ) {
                touch ( $www_dropfile );
                touch ( $www_lastrun );
                echo "HTTP 200 - WWW webhook recieved.";
                die();
        }
        else {
                http_response_code(429);
                echo "HTTP 429 - Rate Limited.";
                die();
                }
        die();

?>
