<?php
include "../config.php";

// Optional: keep the terminal UI flow inside /terminal/ without changing the classic site.
//
// Prefer an explicit flag (terminal=1). As a fallback, detect a terminal embed
// by referrer so the classic site behavior stays unchanged.
$terminalMode = (isset($_REQUEST['terminal']) && (string) $_REQUEST['terminal'] === '1')
    || (isset($_SERVER['HTTP_REFERER']) && strpos((string) $_SERVER['HTTP_REFERER'], '/terminal/') !== false);
$name       = $_GET['contact_name'];
$email      = $_GET['email_address'];
$username   = $_GET['username'];
$interest   = $_GET['interest'];
$pubkey     = $_GET['pubkey'];
$tv         = $_GET['tv'];

$username = strtolower($username);
$pubkey   = trim($pubkey);

$from             = 'From: www-data <www-data@thunix.net>';
$destination_addr = 'newuser@thunix.net';
$subject          = 'New User Registration';
$mailbody         = "A new user has tried to register.
Username:       $username
Real Name:      $name
Email Address:  $email
Interest:       $interest
Pubkey:         $pubkey";

$user_queue = '/dev/shm/userqueue';

$success = 'success1';
if ($tv == 'tildeverse') {
    $success = 'success2';
    if (posix_getpwnam($username)) {
        $success = 'success3';
    }
    $valid_key_starts = ['ssh-rsa', 'ssh-dss', 'ecdsa-sha2', 'ssh-ed25519'];
    $key_parts        = explode(' ', $pubkey, 3);
    if (!in_array($key_parts[0], $valid_key_starts) || count($key_parts) < 2) {
        $success = 'success4';
    }
    if ($success === 'success2') {
        mail($destination_addr, $subject, $mailbody, $from);
        $fp = fopen($user_queue, 'a');
        fwrite($fp, "'$username','$email','$pubkey'\n");
        fclose($fp);
        $fp2 = fopen('/var/signups', 'a');
        fwrite($fp2, 'makeuser ' . $username . ' ' . $email . ' "' . addslashes($pubkey) . "\"\n");
        fclose($fp2);
    }
}

if ($terminalMode) {
    header("Location: $site_root/terminal/view.php?page=$success");
} else {
    header("Location: $site_root/?page=$success");
}
die();
