<?php
/* gitea deploy webhook for thunix*/
/*
 * So, this webhook current accepts hooks for www, ansible, and soon
 * gopher.  It's pretty extensible, and is currently written for gitea,
 * but things like gitlab, github, etc should be feasible, if not
 * downright easy.
 * 
 * While this 'should' work fine with numberic keys, and has been
 * tested, php's loose casting makes it a crap shoot.  We should
 * probably not start tokens with a 0, or a number for that matter?
 * All project hooks need to use the same key.
 * 
 * Also, tildegit's IP address is hard-wired here, so we only accept
 * hooks from tildegit.  This will need that change, if it moves.

/* security */
$access_token     = "secret";
$ansible_lastrun  = '/dev/shm/ansible-hook-last-run';
$ansible_dropfile = '/dev/shm/run-ansible';
$www_lastrun      = '/dev/shm/www-hook-last-run';
$www_dropfile     = '/dev/shm/run-www';
$gopher_lastrun   = '/dev/shm/gopher-hook-last-run';
$gopher_dropfile  = '/dev/shm/run-gopher';
$wiki_lastrun     = '/dev/shm/wiki-hook-last-run';
$wiki_dropfile    = '/dev/shm/run-wiki';

$allowedip        = '198.50.210.248';
$remoteip         = $_SERVER['REMOTE_ADDR'];
$ratelimit        = 300;

/* get json data */
$json = file_get_contents('php://input');
$data = json_decode($json, true);


/* check our token */
$client_token = $data["secret"];
if ( strcmp($client_token, $access_token) !== 0 ) 
{
	http_response_code(403); 
	echo "HTTP 403 - Forbidden, P1.\n";
	exit(0);
}

/* check our source ip for the hook */
if ( strcmp($remoteip, $allowedip) !== 0 )
{
	http_response_code(403);
	echo "HTTP 403 - Forbidden, P2.\n";
	exit(0);
}

// Hook for ansible here
if ($data["repository"]["full_name"] == 'thunix/ansible') {
	syslog(LOG_INFO, 'Ansible Webhook recieved.');
	// We limit runs to once per 5 minutes, so they don't try
	// overlapping.  Systemd shouldn't allow it, but we'll check
	// anyways
	if ( time () - filemtime ( $ansible_lastrun ) > $ratelimit ) {
		touch ( $ansible_dropfile );
		touch ( $ansible_lastrun );
		echo "HTTP 200 - Ansible webhook recieved.\n";
		}
	else {
		http_response_code(429);
		echo "HTTP 429 - Rate Limited.\n";
		exit(0);
		}
}

// Hook for www repo here.  Same rules apply, as above, for www.  We
// could probably make it able to run more frequently.  Backend job is
// just a git pull, and is quick.
elseif ($data["repository"]["full_name"] == 'thunix/www') {
	syslog(LOG_INFO, 'WWW Webhook recieved.');
	if ( time () - filemtime ( $www_lastrun ) > $ratelimit/30 ) {
		touch ( $www_dropfile );
		touch ( $www_lastrun );
		http_response_code(200);
		echo "HTTP 200 - WWW webhook recieved.\n";
		} 
	else {
		http_response_code(429);
		echo "HTTP 429 - Rate Limited.\n";
		exit(0);
		}
}

// Hook for gopher.  Not implemented on the backend yet.
elseif ($data["repository"]["full_name"] == 'thunix/thunix_gopher') {
	syslog(LOG_INFO, 'Gopher Webhook recieved.');
	if ( time () - filemtime ( $gopher_lastrun ) > $ratelimit ) {
		touch ( $gopher_dropfile );
		touch ( $gopher_lastrun );
		http_response_code(200);
		echo "HTTP 200 - Gopher webhook recieved.\n";
		} 
	else {
		http_response_code(429);
		echo "HTTP 429 - Rate Limited.\n";
		exit(0);
		}
}

//Wiki webhook
elseif ($data["repository"]["full_name"] == 'thunix/wiki') {
  syslog(LOG_INFO, 'Wiki Webhook recieved.');
  if ( time () - filemtime ( $wiki_lastrun ) > $ratelimit/30 ) {
    touch ( $wiki_dropfile );
    touch ( $wiki_lastrun );
    http_response_code(200);
    echo "HTTP 200 - Wiki webhook recieved.\n";
    }
  else {
    http_response_code(429);
    echo "HTTP 429 - Rate Limited.\n";
    exit(0);
    }
}

// Easter egg for anyone probing the hook.  Enjoy.  We're a tea pot
// and not a coffee maker :)
else {
	http_response_code(418);
	echo "HTTP 418 - I'm a teapot.\n";
	syslog(LOG_INFO, "Tea Pot Webhook recieved.\n");
	exit(0);
	}

?>

