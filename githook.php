<?php
/*
   $json = '{
  "secret": "01234567890",
  "ref": "refs/heads/master",
  "before": "197b25b76f19c73b2d58873c1b2fdab7d5a27a85",
  "after": "197b25b76f19c73b2d58873c1b2fdab7d5a27a85",
  "compare_url": "",
  "commits": [
    {
      "id": "197b25b76f19c73b2d58873c1b2fdab7d5a27a85",
      "message": "Ok, swap files... le sigh\n",
      "url": "https://tildegit.org/thunix/www/commit/197b25b76f19c73b2d58873c1b2fdab7d5a27a85",
      "author": {
        "name": "Ubergeek",
        "email": "ubergeek@yourtilde.com",
        "username": ""
      },
      "committer": {
        "name": "Ubergeek",
        "email": "ubergeek@yourtilde.com",
        "username": ""
      },
      "verification": null,
      "timestamp": "0001-01-01T00:00:00Z"
    }
  ],
  "repository": {
    "id": 318,
    "owner": {
      "id": 80,
      "login": "thunix",
      "full_name": "Thunix Phoenix Project",
      "email": "",
      "avatar_url": "https://tildegit.org/avatars/9c7f723c8a7fefa9e29995eade157557",
      "language": "",
      "username": "thunix"
    },
    "name": "www",
    "full_name": "thunix/www",
    "description": "This is the code powering the website for thunix",
    "empty": false,
    "private": false,
    "fork": false,
    "parent": null,
    "mirror": false,
    "size": 4997,
    "html_url": "https://tildegit.org/thunix/www",
    "ssh_url": "git@ttm.sh:thunix/www.git",
    "clone_url": "https://tildegit.org/thunix/www.git",
    "website": "",
    "stars_count": 0,
    "forks_count": 3,
    "watchers_count": 2,
    "open_issues_count": 0,
    "default_branch": "master",
    "archived": false,
    "created_at": "2018-12-24T11:54:44-05:00",
    "updated_at": "2019-03-21T20:36:37-04:00",
    "permissions": {
      "admin": false,
      "push": false,
      "pull": false
    }
  },
  "pusher": {
    "id": 33,
    "login": "ubergeek",
    "full_name": "",
    "email": "ubergeek@yourtilde.com",
    "avatar_url": "https://secure.gravatar.com/avatar/113d65c375df5e67b1430596480549a6?d=identicon",
    "language": "en-US",
    "username": "ubergeek"
  },
  "sender": {
    "id": 33,
    "login": "ubergeek",
    "full_name": "",
    "email": "ubergeek@yourtilde.com",
    "avatar_url": "https://secure.gravatar.com/avatar/113d65c375df5e67b1430596480549a6?d=identicon",
    "language": "en-US",
    "username": "ubergeek"
  }
}';
*/

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
$allowedip         = '195.201.242.48';
$remoteip         = $_SERVER['REMOTE_ADDR'];
//$allowedip        = '213.239.234.117';
$ratelimit        = 300;

/* get json data */
$json = file_get_contents('php://input');
$data = json_decode($json, true);


/* check our token */
$client_token = $data["secret"];
//if ((string)$client_token !== (string)$access_token)
if ( strcmp($client_token, $access_token) !== 0 ) 
{
	http_response_code(403); 
	echo "HTTP 403 - Forbidden, P1.\n";
	exit(0);
}

/* check our source ip for the hook */
//if ($remoteip != $allowedip)
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
		//touch ( $ansible_dropfile );
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
	if ( time () - filemtime ( $www_lastrun ) > $ratelimit ) {
		//touch ( $www_dropfile );
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
		//touch ( $gopher_dropfile );
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

// Easter egg for anyone probing the hook.  Enjoy.  We're a coffee maker
// and not a teapot :)
else {
	http_response_code(418);
	echo "HTTP 418 - I'm a teapot.\n";
	exit(0);
	}
?>

